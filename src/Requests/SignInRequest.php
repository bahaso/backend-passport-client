<?php
/**
 * Created by PhpStorm.
 * User: bahasolaptop2
 * Date: 31/07/19
 * Time: 15:43
 */

namespace EdwinFadilah\PassportClient\Requests;


use EdwinFadilah\PassportClient\Requests\Contracts\PassportRequest;

class SignInRequest implements PassportRequest
{
    public $username = "";
    public $email = "";
    public $password = "";
    public $client_id = "";
    public $client_secret = "";
    public $grant_type = "";
    public $scope = "";

    public function setUsername($username)
    {
        return $this->username = $username;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setEmail($email)
    {
        return $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setPassword($password)
    {
        return $this->password = $password;
    }

    public function getPassword()
    {
        return $this->password;
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