<?php

namespace App\Http\Controllers\Web;

class HomeController extends WebBaseController
{
    public function index()
    {
        return $this->responseView('home', [
            'title' => 'Welcome · '.config('app.name'),
        ]);
    }

    public function forbidden()
    {
        return $this->responseForbidden('접근이 제한되었습니다.');
    }

    public function goBack()
    {
        return $this->responseRedirectBack('입력 오류가 있습니다.');
    }

    public function goHome()
    {
        return $this->responseRedirect('home', '홈으로 이동했습니다!');
    }
}
