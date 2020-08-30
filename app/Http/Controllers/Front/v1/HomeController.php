<?php

namespace App\Http\Controllers\Front\v1;

use App\Http\Controllers\Front\WebRootController;
use Illuminate\Http\Request;

class HomeController extends WebRootController
{
    public function index()
    {
        echo "home.index";
    }
}
