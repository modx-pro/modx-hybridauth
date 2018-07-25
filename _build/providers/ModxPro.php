<?php


class Hybrid_Providers_ModxPro extends Hybrid_Provider_Model_OAuth2
{

    public $scope = '';
    /** @var OAuth2Client $api */
    public $api;


    /**
     * @throws Exception
     */
    function initialize()
    {
        parent::initialize();

        $this->api->api_base_url = 'https://id.modx.pro/oauth2/';
        $this->api->authorize_url = $this->api->api_base_url . 'auth';
        $this->api->token_url = $this->api->api_base_url . 'token';

        if (!empty($this->config['redirect_uri'])) {
            $this->api->redirect_uri = $this->config['redirect_uri'];
        }
    }


    /**
     *
     */
    function loginBegin()
    {
        $parameters = [
            'scope' => $this->scope,
            'response_type' => 'code',
            'state' => bin2hex(openssl_random_pseudo_bytes(16)),
        ];
        $optionals = ['scope', 'redirect_uri', 'state'];

        foreach ($optionals as $parameter) {
            if (isset($this->config[$parameter]) && !empty($this->config[$parameter])) {
                $parameters[$parameter] = $this->config[$parameter];
            }
        }
        if (isset($this->config['scope']) && !empty($this->config['scope'])) {
            $this->scope = $this->config['scope'];
        }

        Hybrid_Auth::redirect($this->api->authorizeUrl($parameters));
    }


    /**
     * @return Hybrid_User_Profile
     * @throws Exception
     */
    function getUserProfile()
    {
        $response = $this->api->api('profile', 'POST');
        if (!isset($response->identifier)) {
            throw new Exception("User profile request failed! {$this->providerId} returned an invalid response.", 6);
        }
        $this->user->profile = $response;

        return $this->user->profile;
    }

}

