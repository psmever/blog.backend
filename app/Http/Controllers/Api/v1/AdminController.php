<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\ApiRootController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

use App\Services\v1\AdminsServices;

class AdminController extends ApiRootController
{
    protected $AdminsServices;

    public function __construct(AdminsServices $adminsServices)
    {
        $this->AdminsServices = $adminsServices;
    }
}
