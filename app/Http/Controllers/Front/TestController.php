<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Front\WebRootController;
use Illuminate\Http\Request;

class TestController extends WebRootController
{
    public function index()
    {
        echo "test";
    }
}
