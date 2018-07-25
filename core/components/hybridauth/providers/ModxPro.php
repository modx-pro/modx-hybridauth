<?php

namespace Hybridauth\Provider;

use Hybridauth\Adapter\OAuth2;
use Hybridauth\Exception\Exception;
use Hybridauth\Data\Collection;
use Hybridauth\User\Profile;


class ModxPro extends OAuth2
{
    protected $apiBaseUrl = 'https://id.modx.pro/oauth2';
    protected $authorizeUrl = 'https://id.modx.pro/oauth2/auth';
    protected $accessTokenUrl = 'https://id.modx.pro/oauth2/token';


    /**
     * @return bool|Profile
     * @throws Exception
     * @throws \Hybridauth\Exception\HttpClientFailureException
     * @throws \Hybridauth\Exception\HttpRequestFailedException
     * @throws \Hybridauth\Exception\InvalidAccessTokenException
     */
    function getUserProfile()
    {
        $data = new Collection($this->apiRequest('profile', 'POST'));

        if (!$data->exists('identifier')) {
            throw new Exception('User profile request failed! ModxPro returned an invalid response.');
        }

        $userProfile = new Profile();
        $userProfile->identifier = $data->get('identifier');
        $userProfile->email = $data->get('email');
        $userProfile->displayName = $data->get('displayName');
        $userProfile->photoURL = $data->get('photoURL');
        $userProfile->webSiteURL = $data->get('webSiteURL');
        $userProfile->profileURL = $data->get('profileUrl');
        $userProfile->phone = $data->get('phone');
        $userProfile->address = $data->get('address');
        $userProfile->region = $data->get('region');
        $userProfile->city = $data->get('city');
        $userProfile->zip = $data->get('zip');

        return $userProfile;
    }

}

