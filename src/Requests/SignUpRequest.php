<?php
/**
 * Created by PhpStorm.
 * User: bahasolaptop2
 * Date: 31/07/19
 * Time: 15:57
 */

namespace EdwinFadilah\PassportClient\Requests;


use EdwinFadilah\PassportClient\Requests\Contracts\PassportRequest;

class SignUpRequest implements PassportRequest
{
    public $email = "";
    public $password = "";
    public $firstname = "";
    public $lastname = "";
    public $gender = "";
    public $cellphonenumber = "";
    public $country_id = "";
    public $city_id = "";
    public $birthday = "";
    public $client_id = "";
    public $client_secret = "";
    public $grant_type = "";
    public $scope = "";


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

    public function setFirstName($firstname)
    {
        return $this->firstname = $firstname;
    }

    public function getFirstName()
    {
        return $this->firstname;
    }

    public function setLastName($lastname)
    {
        return $this->lastname = $lastname;
    }

    public function getLastName()
    {
        return $this->lastname;
    }

    public function setGender($gender)
    {
        return $this->gender = $gender;
    }

    public function getGender()
    {
        return $this->gender;
    }

    public function setCellPhoneNumber($cellphonenumber)
    {
        return $this->cellphonenumber = $cellphonenumber;
    }

    public function getCellPhoneNumber()
    {
        return $this->cellphonenumber;
    }

    public function setCountryId($country_id)
    {
        return $this->country_id = $country_id;
    }

    public function getCountryId()
    {
        return $this->country_id;
    }

    public function setCityId($city_id)
    {
        return $this->city_id = $city_id;
    }

    public function getCityId()
    {
        return $this->city_id;
    }

    public function setBirthday($birthday)
    {
        return $this->birthday = $birthday;
    }

    public function getBirthDay()
    {
        return $this->birthday;
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