<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Response;
use App\Traits\WebResponseTrait;

class WebBaseController extends Controller
{
    use WebResponseTrait;
}
