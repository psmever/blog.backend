<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
    // first Test Controller
    public function test()
    {
        return [
            "Test" => "test"
        ];
    }
}
