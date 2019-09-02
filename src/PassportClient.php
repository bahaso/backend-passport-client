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
use Bahaso\PassportClient\Requests\SignInRequest;
use Bahaso\PassportClient\Requests\SignUpRequest;
use Bahaso\PassportClient\Requests\SocialAuthRequest;
use Bahaso\PassportClient\Responses\GetUserResponse;
use Bahaso\PassportClient\Responses\Response;
use Bahaso\PassportClient\Responses\SignInResponse;
use Bahaso\PassportClient\Responses\SignUpResponse;
use Illuminate\Http\Request;

class PassportClient
{
    const GRANT_TYPE_PASSWORD = "password";
    const GRANT_TYPE_SOCIAL = "social";
    const GRANT_TYPE_CLIENT_CREDENTIALS = "client_credentials";
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

    public function prepareSignInRequest($request)
    {
        $this->validateRequestForSignIn($request);
        $passport_sign_in_request = new SignInRequest();
        if ($request instanceof Request) {
            $passport_sign_in_request->setUsername($request->get("username"));
            $passport_sign_in_request->setPassword($request->get("password"));
            $passport_sign_in_request->setClientId(config('passport.client_id'));
            $passport_sign_in_request->setClientSecret(config('passport.client_secret'));
            $passport_sign_in_request->setGrantType($request->get("grant_type"));
            $passport_sign_in_request->setScope("*");
        }
        else if (is_array($request)) {
            $passport_sign_in_request->setUsername($request["username"]);
            $passport_sign_in_request->setPassword($request["password"]);
            $passport_sign_in_request->setClientId(config('passport.client_id'));
            $passport_sign_in_request->setClientSecret(config('passport.client_secret'));
            $passport_sign_in_request->setGrantType($request["grant_type"]);
            $passport_sign_in_request->setScope("*");
        }

        return $passport_sign_in_request;
    }

    private function validateRequestForSignIn($request)
    {
        $keys = ["username", "password", "grant_type"];
        $errors = [];
        if ($request instanceof Request) {
            foreach ($keys as $key) {
                if ($request->get($key) == null) {
                    $errors[$key] = [$key . ' parameter is missing'];
                }
            }
        }
        else if (is_array($request)) {
            foreach ($keys as $key) {
                if (isset($request[$key])) {
                    if (empty($request[$key])) $errors[$key] = [$key . ' parameter is missing'];
                }
            }
        }

        if (count($errors) > 0)
            throw new InvalidRequestException(422, "Bad Request, missing parameter(s)", $errors);
    }
    
    public function login(SignInRequest $request)
    {
        $http = $this->prepareHttpClient();
        $body = $this->prepareRequestBody($request);
        $headers = $this->prepareRequestHeader();
        $options = array_merge($body, $headers);

        try {
            $request = $http->request(
                'post',
                config('passport.sign_in_url', 'https://passportserver.dev.io/api/signin'),
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

    public function prepareSignUpRequest($request)
    {
        $this->validateRequestForSignUp($request);
        $passport_sign_in_request = new SignUpRequest();
        if ($request instanceof Request) {
            $passport_sign_in_request->setEmail($request->get("email"));
            $passport_sign_in_request->setPassword($request->get("password"));
            $passport_sign_in_request->setFirstName($request->get("firstname"));
            $passport_sign_in_request->setLastName($request->get("lastname"));
            $passport_sign_in_request->setGender($request->get("gender"));
            $passport_sign_in_request->setCellPhoneNumber($request->get("cellphonenumber"));
            $passport_sign_in_request->setCountryId($request->get("countryd_id"));
            $passport_sign_in_request->setCityId($request->get("city_id"));
            $passport_sign_in_request->setBirthday($request->get("birthday"));
            $passport_sign_in_request->setClientId(config('passport.client_id'));
            $passport_sign_in_request->setClientSecret(config('passport.client_secret'));
            $passport_sign_in_request->setGrantType($request->get("grant_type"));
            $passport_sign_in_request->setScope("*");
        }
        else if (is_array($request)) {
            $passport_sign_in_request->setEmail($request["email"]);
            $passport_sign_in_request->setPassword($request["password"]);
            $passport_sign_in_request->setFirstName($request["firstname"]);
            $passport_sign_in_request->setLastName($request["lastname"]);
            $passport_sign_in_request->setGender($request["gender"]);
            $passport_sign_in_request->setCellPhoneNumber($request["cellphonenumber"]);
            $passport_sign_in_request->setCountryId($request["country_id"]);
            $passport_sign_in_request->setCityId($request["city_id"]);
            $passport_sign_in_request->setBirthday($request["birthday"]);
            $passport_sign_in_request->setClientId(config('passport.client_id'));
            $passport_sign_in_request->setClientSecret(config('passport.client_secret'));
            $passport_sign_in_request->setGrantType($request["grant_type"]);
            $passport_sign_in_request->setScope("*");
        }

        return $passport_sign_in_request;
    }

    private function validateRequestForSignUp($request)
    {
        $keys = ["firstname", "lastname", "email", "password", "gender", "cellphonenumber", "birthday", "country_id", "city_id", "grant_type"];
        $errors = [];
        if ($request instanceof Request) {
            foreach ($keys as $key) {
                if ($request->get($key) == null) {
                    $errors[$key] = $key . ' parameter is missing';
                }
            }
        }
        else if (is_array($request)) {
            foreach ($keys as $key) {
                if (! isset($request[$key])) $errors[$key] = [$key . ' parameter is missing'];
                else if (empty($request[$key])) $errors[$key] = [$key . ' parameter is missing'];
            }
        }

        if (count($errors) > 0)
            throw new InvalidRequestException(422, "Bad Request, missing parameter(s)", $errors);
    }

    public function register(SignUpRequest $request)
    {
        $http = $this->prepareHttpClient();
        $body = $this->prepareRequestBody($request);
        $headers = $this->prepareRequestHeader();
        $options = array_merge($body, $headers);

        try {
            $request = $http->request(
                'post',
                config('passport.sign_up_url', 'https://passportserver.dev.io/api/signup'),
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
        if ($request->getGrantType() == self::GRANT_TYPE_PASSWORD || $request->getGrantType() == self::GRANT_TYPE_SOCIAL) {
            return [
                "form_params" => (array) $request
            ];
        }
        else {
            throw new InvalidGrantTypeException(422, "Grant type " . $request->getGrantType() . " is not supported");
        }
    }

    private function handleAuthServerSignUpResponse($response)
    {
        if ($response['code'] !== 200) {
            $this->handleAuthServerResponseException($response);
        }
        return new SignUpResponse($response['code'], true, $response['message'], $response['data']);
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
                config('passport.get_user_from_token_url', 'https://passportserver.dev.io/api/token/user'),
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

    public function prepareSocialAuthRequest($request)
    {
        $passport_sign_in_request = new SocialAuthRequest();
        if ($request instanceof Request) {
            $access_token = $request->get("fb_access_token") ? $request->get("fb_access_token") : $request->get("auth_code", "");
            $passport_sign_in_request->setAccessToken($access_token);
            $passport_sign_in_request->setClientId(config('passport.client_id'));
            $passport_sign_in_request->setClientSecret(config('passport.client_secret'));
            $passport_sign_in_request->setGrantType($request->get("grant_type"));
            $passport_sign_in_request->setScope("*");
        }
        else if (is_array($request)) {
            $access_token = isset($request["fb_access_token"]) ? $request["fb_access_token"] : (isset($request["auth_code"]) ? $request["auth_code"] : "");
            $passport_sign_in_request->setAccessToken($access_token);
            $passport_sign_in_request->setClientId(config('passport.client_id'));
            $passport_sign_in_request->setClientSecret(config('passport.client_secret'));
            $passport_sign_in_request->setGrantType($request["grant_type"]);
            $passport_sign_in_request->setScope("*");
        }

        return $passport_sign_in_request;
    }

    public function socialAuth(SocialAuthRequest $request, $provider)
    {
        $http = $this->prepareHttpClient();
        $body = $this->prepareRequestBody($request);
        $headers = $this->prepareRequestHeader();
        $options = array_merge($body, $headers);

        if ($provider == self::FACEBOOK_PROVIDER)
            $url = config('passport.facebook_auth_url', 'https://passportserver.dev.io/api/auth/facebook');
        else if ($provider == self::GOOGLE_PROVIDER)
            $url = config('passport.facebook_auth_url', 'https://passportserver.dev.io/api/auth/google');
        else
            $url = "https://passportserver.dev.io/api/signin";

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
}
