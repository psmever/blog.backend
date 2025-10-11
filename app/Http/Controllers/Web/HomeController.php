<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Web\WebBaseController;

class HomeController extends WebBaseController
{
    public function index()
    {
        return $this->render('home', [
            'title' => 'Home · ' . config('app.name'),
        ]);
    }
}
