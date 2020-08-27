<?php

namespace App\Services\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AdminsServices
{

    public function attemptAdminLogin(Request $request)
    {
        // TODO 2020-08-27 23:50  관리자 로그인 처리.

        if(!Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
            throw new \App\Exceptions\CustomException(__('default.exception.loginFail'));
        }

        $user = Auth::user();
        dd($user);
    }
}
