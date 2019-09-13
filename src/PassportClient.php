<?php
/**
 * Created by PhpStorm.
 * User: bahasolaptop2
 * Date: 30/07/19
 * Time: 15:07
 */

namespace Bahaso\PassportClient;


use Bahaso\PassportClient\Exceptions\InvalidGrantTypeException;
use Bahaso\PassportClient\Exceptions\InvalidRequestException;
use Bahaso\PassportClient\Exceptions\ServerResponseException;
use Bahaso\PassportClient\Requests\Contracts\PassportRequest;
use Bahaso\PassportClient\Requests\LoginRequest;
use Bahaso\PassportClient\Requests\RegisterRequest;
use Bahaso\PassportClient\Requests\SignInRequest;
use Bahaso\PassportClient\Requests\SignUpRequest;
use Bahaso\PassportClient\Requests\SocialAuthRequest;
use Bahaso\PassportClient\Requests\ValidateOTPRequest;
use Bahaso\PassportClient\Responses\GetUserResponse;
use Bahaso\PassportClient\Responses\Response;
use Bahaso\PassportClient\Responses\SignInResponse;

class PassportClient
{
    const GRANT_TYPE_PASSWORD = "password";
    const GRANT_TYPE_SOCIAL = "social";
    const GRANT_TYPE_CLIENT_CREDENTIALS = "client_credentials";
    const GRANT_TYPE_AUTH_OTP_CODE = "authorization_otp_code";
    const FACEBOOK_PROVIDER = "facebook";
    const GOOGLE_PROVIDER = "google";

    protected function prepareHttpClient()
    {
        $client = new \GuzzleHttp\Client([
            'verify' => false
        ]);

        return $client;
    }

    private function prepareRequestHeader()
    {
        return [
            'headers' => [
                'Accept' => 'application/json'
            ]
        ];
    }

    public function testConnection()
    {
        $client = $this->prepareHttpClient();

        $response = $client->get(config('passport.test_connection_url', 'https://passporization.dev.io/api/connection/test'));

        if ($response->getStatusCode() == 200) {
            return new Response($response->getStatusCode(), true, "You are connected with Auth Server", json_decode($response->getBody()));
        }
        else {
            throw new ServerResponseException(500, "Something wrong with Authorization Server");
        }
    }

    public function prepareSignInRequest($client_id, $client_secret, $username, $password, $grant_type, $scope)
    {
        return
            new SignInRequest(
                $client_id,
                $client_secret,
                $username,
                $password,
                $grant_type,
                $scope
            );
    }
    
    public function signIn(SignInRequest $request)
    {
        $http = $this->prepareHttpClient();
        $body = $this->prepareRequestBody($request);
        $headers = $this->prepareRequestHeader();
        $options = array_merge($body, $headers);

        try {
            $request = $http->request(
                'post',
                config('passport.sign_in_url', ''),
                $options);
        }
        catch (\Exception $exception) {
            throw new ServerResponseException($exception->getCode(), $exception->getMessage());
        }

        $response = json_decode((string) $request->getBody(), true);

        return $this->handleAuthServerSignInResponse($response);
    }

    private function handleAuthServerSignInResponse($response)
    {
        if ($response['code'] !== 200) {
            $this->handleAuthServerResponseException($response);
        }
        return new SignInResponse($response['code'], true, $response['message'], $response['data']);
    }

    public function prepareSignUpRequest(
        $client_id,
        $client_secret,
        $email,
        $password,
        $first_name,
        $last_name,
        $gender,
        $cell_phone_number,
        $country_id,
        $city_id,
        $birthday,
        $grant_type,
        $scope
    )
    {
        return new SignUpRequest(
                $client_id,
                $client_secret,
                $email,
                $password,
                $first_name,
                $last_name,
                $gender,
                $cell_phone_number,
                $country_id,
                $city_id,
                $birthday,
                $grant_type,
                $scope
        );
    }

    public function signUp(SignUpRequest $request)
    {
        $http = $this->prepareHttpClient();
        $body = $this->prepareRequestBody($request);
        $headers = $this->prepareRequestHeader();
        $options = array_merge($body, $headers);

        try {
            $request = $http->request(
                'post',
                config('passport.sign_up_url', ''),
                $options);
        }
        catch (\Exception $exception) {
            throw new ServerResponseException($exception->getCode(), $exception->getMessage());
        }

        $response = json_decode((string) $request->getBody(), true);

        return $this->handleAuthServerSignInResponse($response);
    }

    private function prepareRequestBody(PassportRequest $request)
    {
        if ($request->getGrantType() == self::GRANT_TYPE_PASSWORD || $request->getGrantType() == self::GRANT_TYPE_SOCIAL || $request->getGrantType() == self::GRANT_TYPE_AUTH_OTP_CODE) {
            return [
                "form_params" => (array) $request
            ];
        }
        else {
            throw new InvalidGrantTypeException(422, "Grant type " . $request->getGrantType() . " is not supported");
        }
    }

    private function handleAuthServerResponseException($response)
    {
        $errors = isset($response['errors']) ? $response['errors'] : [];
        throw new ServerResponseException($response['code'], $response['message'], $errors);
    }

    public function validateAccessToken($access_token = null)
    {
        if (! $access_token) $access_token = $this->getAccessTokenFromHeader();
        return $this->getUserFromToken($access_token);
    }

    public function getAccessTokenFromHeader()
    {
        $header = request()->header('Authorization');
        if (! $header) throw new InvalidRequestException(422, "Missing Authorization on header request");
        if (strpos("Bearer", $header) !== false) throw new InvalidRequestException(422, "Missing Bearer on header request");
        return explode(" ", $header)[1];
    }

    public function getUserFromToken($access_token)
    {
        $http = $this->prepareHttpClient();
        $headers = $this->prepareRequestHeaderWithToken($access_token);
        $options = array_merge($headers);

        try {
            $request = $http->request(
                'get',
                config('passport.get_user_from_token_url', ''),
                $options);
        }
        catch (\Exception $exception) {
            throw new ServerResponseException($exception->getCode(), $exception->getMessage());
        }

        $response = json_decode((string) $request->getBody(), true);

        return $this->handleAuthServerGetUserResponse($response);
    }

    private function prepareRequestHeaderWithToken($access_token)
    {
        return [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $access_token
            ]
        ];
    }

    private function handleAuthServerGetUserResponse($response)
    {
        if ($response['code'] !== 200) {
            $this->handleAuthServerResponseException($response);
        }
        return new GetUserResponse($response['code'], true, $response['message'], $response['data']);
    }

    public function prepareSocialAuthRequest($client_id, $client_secret, $access_token, $grant_type, $scope)
    {
        return
            new SocialAuthRequest(
                $client_id,
                $client_secret,
                $access_token,
                $grant_type,
                $scope
            );
    }

    public function socialAuth(SocialAuthRequest $request, $provider)
    {
        $http = $this->prepareHttpClient();
        $body = $this->prepareRequestBody($request);
        $headers = $this->prepareRequestHeader();
        $options = array_merge($body, $headers);

        if ($provider == self::FACEBOOK_PROVIDER)
            $url = config('passport.facebook_auth_url', '');
        else if ($provider == self::GOOGLE_PROVIDER)
            $url = config('passport.google_auth_url', '');
        else
            $url = "notfound";

        try {
            $request = $http->request(
                'post',
                $url,
                $options);
        }
        catch (\Exception $exception) {
            throw new ServerResponseException($exception->getCode(), $exception->getMessage());
        }

        $response = json_decode((string) $request->getBody(), true);

        return $this->handleAuthServerSignInResponse($response);
    }

    public function checkToken($access_token, $scope)
    {
        $http = $this->prepareHttpClient();
        $headers = $this->prepareRequestHeaderWithToken($access_token);
        $options = array_merge($headers);
        
        try {
            $request = $http->request(
                'get',
                config('passport.check_token_url', '').'?scope='.$scope,
                $options);
        }
        catch (\Exception $exception) {
            throw new ServerResponseException($exception->getCode(), $exception->getMessage());
        }

        $response = json_decode((string) $request->getBody(), true);

        return $this->handleAuthServerGetUserResponse($response);
    }

    public function register(RegisterRequest $request)
    {
        $http = $this->prepareHttpClient();
        $body = [
            "form_params" => (array) $request
        ];
        $headers = $this->prepareRequestHeader();
        $options = array_merge($body, $headers);

        try {
            $request = $http->request(
                'post',
                config('passport.register_url', ''),
                $options);
        }
        catch (\Exception $exception) {
            throw new ServerResponseException($exception->getCode(), $exception->getMessage());
        }

        $response = json_decode((string) $request->getBody(), true);

        return $this->handleAuthServerSignInResponse($response);
    }

    public function login(LoginRequest $request)
    {
        $http = $this->prepareHttpClient();
        $body = [
            "form_params" => (array) $request
        ];
        $headers = $this->prepareRequestHeader();
        $options = array_merge($body, $headers);

        try {
            $request = $http->request(
                'post',
                config('passport.log_in_url', ''),
                $options);
        }
        catch (\Exception $exception) {
            throw new ServerResponseException($exception->getCode(), $exception->getMessage());
        }

        $response = json_decode((string) $request->getBody(), true);

        return $this->handleAuthServerSignInResponse($response);
    }

    public function validateOTP(ValidateOTPRequest $request)
    {
        $http = $this->prepareHttpClient();
        $body = $this->prepareRequestBody($request);
        $headers = $this->prepareRequestHeader();
        $options = array_merge($body, $headers);

        try {
            $request = $http->request(
                'post',
                config('passport.validate_otp_url', ''),
                $options);
        }
        catch (\Exception $exception) {
            throw new ServerResponseException($exception->getCode(), $exception->getMessage());
        }

        $response = json_decode((string) $request->getBody(), true);

        return $this->handleAuthServerSignInResponse($response);
    }
}
