<?php

namespace App\Http\Controllers\Front\v1;

use App\Http\Controllers\Front\WebRootController;
use Illuminate\Http\Request;

class HomeController extends WebRootController
{
    /**
     * 기본 인덱스.
     *
     * @return void
     */
    public function index()
    {
        return view('landing');
    }

    /**
     * admin 로그인 페이지.
     *
     * @return void
     */
    public function login()
    {
        return view('v1/login');
    }
}
