<?php

namespace App\Services\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Repositories\v1\PassportRepository;

class AuthServices
{
    protected $currentRequest;
    protected $passportRepository;

    function __construct(Request $request, PassportRepository $passportRepository){
        $this->currentRequest = $request;
        $this->passportRepository = $passportRepository;
    }

    public function attemptLogin()
    {
        // TODO 2020-08-27 23:50  관리자 로그인 처리.
        // if(!Auth::attempt(['email' => $this->currentRequest->input('email'), 'password' => $this->currentRequest->input('password')])) {
        //     // throw new \App\Exceptions\CustomException(__('default.exception.loginFail'));
        // }

        // return $this->publishNewToken();
    }

    public function publishNewToken()
    {
        // $client = $this->passportRepository->clientInfo();
        // print_r($client);

        // $payloadObject = [
        //     'grant_type' => 'password',
        //     'client_id' => $client->client_id,
        //     'client_secret' => $client->client_secret,
        //     'username' => $this->currentRequest->input('email'),
        //     'password' => $this->currentRequest->input('password'),
        //     'scope' => '',
        // ];

        // $tokenRequest = Request::create('/oauth/token', 'POST', $payloadObject);
        // $tokenRequestResult = json_decode(app()->handle($tokenRequest)->getContent());

        // if(isset($tokenRequestResult->error_message) && $tokenRequestResult->error_message) {
        //     throw new \App\Exceptions\CustomException($tokenRequestResult->error_message);
        // }

        // return $tokenRequestResult;
    }
}
