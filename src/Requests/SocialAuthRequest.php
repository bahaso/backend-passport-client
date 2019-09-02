<?php
/**
 * Created by PhpStorm.
 * User: bahasolaptop2
 * Date: 31/07/19
 * Time: 15:43
 */

namespace Bahaso\PassportClient\Requests;


use Bahaso\PassportClient\Requests\Contracts\PassportRequest;

class SocialAuthRequest implements PassportRequest
{
    public $access_token = "";
    public $client_id = "";
    public $client_secret = "";
    public $grant_type = "";
    public $scope = "";

    public function setAccessToken($access_token)
    {
        return $this->access_token = $access_token;
    }

    public function getAccessToken()
    {
        return $this->access_token;
    }

    public function setClientId($client_id)
    {
        return $this->client_id = $client_id;
    }

    public function getClientId()
    {
        return $this->client_id;
    }

    public function setClientSecret($client_secret)
    {
        return $this->client_secret = $client_secret;
    }

    public function getClientSecret()
    {
        return $this->client_secret;
    }

    public function setGrantType($grant_type)
    {
        return $this->grant_type = $grant_type;
    }

    public function getGrantType()
    {
        return $this->grant_type;
    }

    public function setScope($scope)
    {
        return $this->scope = $scope;
    }

    public function getScope()
    {
        return $this->scope;
    }
}
