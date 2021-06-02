<?php

namespace App\Http\Controllers\Front\v1;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * 기본 랜딩 페이지.
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('landing');
    }
}
