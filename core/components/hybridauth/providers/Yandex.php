<?php

namespace Hybridauth\Provider;

use Hybridauth\Adapter\OAuth2;
use Hybridauth\Exception\Exception;
use Hybridauth\Data\Collection;
use Hybridauth\User\Profile;


class Yandex extends OAuth2
{
    protected $apiBaseUrl = 'https://login.yandex.ru/info';
    protected $authorizeUrl = 'https://oauth.yandex.ru/authorize';
    protected $accessTokenUrl = 'https://oauth.yandex.ru/token';


    /**
     * @return bool|Profile
     * @throws Exception
     * @throws \Hybridauth\Exception\HttpClientFailureException
     * @throws \Hybridauth\Exception\HttpRequestFailedException
     * @throws \Hybridauth\Exception\InvalidAccessTokenException
     */
    public function getUserProfile()
    {
        $data = new Collection($this->apiRequest('', 'GET'));
        if (!$data->exists('id')) {
            throw new Exception('User profile request failed! ModxPro returned an invalid response.');
        }
        $userProfile = new Profile();

        $userProfile->identifier = $data->get('id');
        $userProfile->firstName = $data->get('real_name');
        $userProfile->lastName = $data->get('family_name');
        $userProfile->displayName = $data->get('display_name');
        $userProfile->photoURL = 'http://upics.yandex.net/' . $userProfile->identifier . '/normal';
        $userProfile->profileURL = '';
        $userProfile->gender = (int)$data->get('sex') == 'female';
        $userProfile->email =
        $userProfile->emailVerified = $data->get('default_email');

        if ($dob = $data->get('birthday')) {
            list($userProfile->birthYear, $userProfile->birthMonth, $userProfile->birthDay) = explode('-', $dob);
        }

        return $userProfile;
    }
}

