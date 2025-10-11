<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Response;

class WebBaseController extends Controller
{
    /**
     * ✅ 기본 view 렌더링 메서드
     */
    protected function render(string $view, array $data = []): View|Factory|Application
    {
        return view($view, $data);
    }

    /**
     * ⚠️ 페이지 not found (404)
     */
    protected function notFound(string $message = 'Page not found'): Response
    {
        return response()->view('errors.404', ['message' => $message], 404);
    }

    /**
     * ❌ 서버 내부 오류 (500)
     */
    protected function serverError(string $message = 'Something went wrong'): Response
    {
        return response()->view('errors.500', ['message' => $message], 500);
    }
}
