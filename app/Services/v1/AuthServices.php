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

    /**
     * 로그인 시도.
     *
     * @return void
     */
    public function attemptLogin()
    {
        $request = $this->currentRequest;

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
			'password' => 'required',
        ],
        [
            'email.required'=> __('default.login.email_required'),
            'email.email'=> __('default.login.email_not_validate'),
            'email.exists'=> __('default.login.email_exists'),
            'password.required'=> __('default.login.password_required'),
         ]);

		if( $validator->fails() ) {
            throw new \App\Exceptions\CustomException($validator->errors()->first()); // 로그인 실패.
        }

        if(!Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
            throw new \App\Exceptions\CustomException(__('default.login.password_fail')); // 비밀번호 실패.
        }

        return $this->publishNewToken();
    }

    /**
     * Passport New Token 발행.
     *
     * @return void
     */
    public function publishNewToken()
    {
        $client = $this->passportRepository->clientInfo();

        $payloadObject = [
            'grant_type' => 'password',
            'client_id' => $client->client_id,
            'client_secret' => $client->client_secret,
            'username' => $this->currentRequest->input('email'),
            'password' => $this->currentRequest->input('password'),
            'scope' => '',
        ];

        $tokenRequest = Request::create('/oauth/token', 'POST', $payloadObject);
        $tokenRequestResult = json_decode(app()->handle($tokenRequest)->getContent());

        if(isset($tokenRequestResult->error_message) && $tokenRequestResult->error_message) {
            throw new \App\Exceptions\CustomException($tokenRequestResult->error_message);
        }

        return $tokenRequestResult;
    }
}
