<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Web\WebBaseController;

class HomeController extends WebBaseController
{
    public function index()
    {
        return view('home', [   // 👈 'welcome' 대신 'home' 으로 변경
            'title' => 'Home · ' . config('app.name'),
        ]);
    }
}
