<?php

/**
 * VK ID OAuth 2.1 provider for Hybridauth (local classmap).
 *
 * @see https://id.vk.com/about/business/go/docs/ru/vkid/latest/vk-id/connection/start-integration/auth-without-sdk/auth-without-sdk-web
 * @see https://dev.vk.com/ru/vkid
 */

namespace Hybridauth\Provider;

use Hybridauth\Adapter\OAuth2;
use Hybridauth\Data;
use Hybridauth\Exception\InvalidAccessTokenException;
use Hybridauth\Exception\InvalidApplicationCredentialsException;
use Hybridauth\Exception\UnexpectedApiResponseException;
use Hybridauth\User;

/**
 * VK ID OAuth 2.1 adapter (authorization code + PKCE).
 *
 * Legacy api.vk.com/oauth stays in {@see Vkontakte}. New apps: ha.keys.VkId.
 */
class VkId extends OAuth2
{
    /**
     * {@inheritdoc}
     */
    protected $scope = 'vkid.personal_info email';

    /**
     * {@inheritdoc}
     */
    protected $apiBaseUrl = 'https://id.vk.ru/';

    /**
     * {@inheritdoc}
     */
    protected $authorizeUrl = 'https://id.vk.ru/authorize';

    /**
     * {@inheritdoc}
     */
    protected $accessTokenUrl = 'https://id.vk.ru/oauth2/auth';

    /**
     * {@inheritdoc}
     */
    protected $apiDocumentation =
        'https://id.vk.com/about/business/go/docs/ru/vkid/latest/vk-id/connection/start-integration/auth-without-sdk/auth-without-sdk-web';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->clientId = $this->config->filter('keys')->get('id')
            ?: $this->config->filter('keys')->get('key');
        $this->clientSecret = (string)($this->config->filter('keys')->get('secret') ?: '');

        if (!$this->clientId) {
            throw new InvalidApplicationCredentialsException(
                'Your application id is required in order to connect to ' . $this->providerId
            );
        }

        $this->scope = $this->config->exists('scope') ? $this->config->get('scope') : $this->scope;

        if ($this->config->exists('tokens')) {
            $this->setAccessToken($this->config->get('tokens'));
        }

        if ($this->config->exists('supportRequestState')) {
            $this->supportRequestState = $this->config->get('supportRequestState');
        }

        $this->setCallback($this->config->get('callback'));
        $this->setApiEndpoints($this->config->get('endpoints'));
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        parent::initialize();

        $codeVerifier = $this->getStoredData('codeVerifier');
        if (!$codeVerifier) {
            $codeVerifier = $this->generateCodeVerifier();
            $this->storeData('codeVerifier', $codeVerifier);
        }

        $this->AuthorizeUrlParameters += [
            'code_challenge' => $this->generateCodeChallenge($codeVerifier),
            'code_challenge_method' => 'S256',
        ];

        $this->tokenExchangeParameters = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'redirect_uri' => $this->callback,
            'code_verifier' => $codeVerifier,
        ];
        if ($this->clientSecret !== '') {
            $this->tokenExchangeParameters['client_secret'] = $this->clientSecret;
        }

        $this->tokenExchangeHeaders = [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];

        if ($this->isRefreshTokenAvailable()) {
            $this->tokenRefreshParameters = [
                'grant_type' => 'refresh_token',
                'refresh_token' => $this->getStoredData('refresh_token'),
                'client_id' => $this->clientId,
                'device_id' => (string)$this->getStoredData('device_id'),
                'state' => (string)$this->getStoredData('authorization_state'),
            ];
            if ($this->clientSecret !== '') {
                $this->tokenRefreshParameters['client_secret'] = $this->clientSecret;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function exchangeCodeForAccessToken($code)
    {
        $inputType = $_SERVER['REQUEST_METHOD'] === 'POST' ? INPUT_POST : INPUT_GET;
        $deviceId = (string)$this->filterInput($inputType, 'device_id');
        $state = (string)$this->filterInput($inputType, 'state');

        if ($deviceId !== '') {
            $this->storeData('device_id', $deviceId);
            $this->tokenExchangeParameters['device_id'] = $deviceId;
        }
        if ($state !== '') {
            $this->tokenExchangeParameters['state'] = $state;
        }

        $this->tokenExchangeParameters['code'] = $code;
        $this->tokenExchangeParameters['code_verifier'] = $this->getStoredData('codeVerifier');

        $response = $this->httpClient->request(
            $this->accessTokenUrl,
            $this->tokenExchangeMethod,
            $this->tokenExchangeParameters,
            $this->tokenExchangeHeaders
        );

        $this->validateApiResponse('Unable to exchange code for API access token');

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    protected function validateAccessTokenExchange($response)
    {
        $collection = parent::validateAccessTokenExchange($response);

        if ($collection->exists('user_id')) {
            $this->storeData('user_id', $collection->get('user_id'));
        }
        if ($collection->exists('id_token')) {
            $this->storeData('id_token', $collection->get('id_token'));
        }

        $this->deleteStoredData('codeVerifier');

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserProfile()
    {
        $response = $this->httpClient->request(
            'https://id.vk.ru/oauth2/user_info',
            'POST',
            [
                'client_id' => $this->clientId,
                'access_token' => $this->getStoredData('access_token'),
            ],
            [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ]
        );

        $this->validateApiResponse('Unable to fetch user profile');

        $data = (new Data\Parser())->parse($response);
        $collection = new Data\Collection($data);

        if ($collection->exists('error')) {
            throw new InvalidAccessTokenException(
                'Provider returned an error: ' . $collection->get('error')
                . ' — ' . ($collection->get('error_description') ?: 'Unknown error')
            );
        }

        $user = $collection->get('user');
        if (!$user) {
            throw new UnexpectedApiResponseException('Provider API returned an unexpected response.');
        }
        $user = new Data\Collection($user);

        $userProfile = new User\Profile();
        $userProfile->identifier = (string)($user->get('user_id') ?: $this->getStoredData('user_id'));
        $userProfile->firstName = $user->get('first_name');
        $userProfile->lastName = $user->get('last_name');
        $userProfile->displayName = trim($userProfile->firstName . ' ' . $userProfile->lastName);
        $userProfile->email = $user->get('email');
        $userProfile->emailVerified = $userProfile->email;
        $userProfile->phone = $user->get('phone');
        $userProfile->photoURL = $user->get('avatar');

        if ($userProfile->identifier !== '') {
            $userProfile->profileURL = 'https://vk.com/id' . $userProfile->identifier;
        }

        switch ((int)$user->get('sex')) {
            case 1:
                $userProfile->gender = 'female';
                break;
            case 2:
                $userProfile->gender = 'male';
                break;
        }

        $birthday = (string)$user->get('birthday');
        if ($birthday !== '' && preg_match('/^(\d{2})\.(\d{2})\.(\d{4})$/', $birthday, $m)) {
            $userProfile->birthDay = (int)$m[1];
            $userProfile->birthMonth = (int)$m[2];
            $userProfile->birthYear = (int)$m[3];
        }

        return $userProfile;
    }

    /**
     * @return string
     */
    protected function generateCodeVerifier()
    {
        $random = random_bytes(64);

        return rtrim(strtr(base64_encode($random), '+/', '-_'), '=');
    }

    /**
     * @param string $codeVerifier
     * @return string
     */
    protected function generateCodeChallenge($codeVerifier)
    {
        return rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');
    }
}
