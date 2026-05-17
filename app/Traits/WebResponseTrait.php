<?php

namespace App\Traits;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

trait WebResponseTrait
{
    /**
     * ✅ View 렌더링 (표준화)
     * Blade 파일을 지정해 렌더링합니다.
     *
     * @param  string  $view  Blade 파일 경로 (예: 'home' or 'errors.404')
     * @param  array  $data  View에 전달할 데이터
     */
    protected function responseView(
        string $view,
        array $data = []
    ): View|Factory|Application {
        return view($view, $data);
    }

    /**
     * 🔁 Redirect (라우트 이름 기준)
     * 성공 메시지 등 flash message를 함께 전달합니다.
     *
     * @param  string  $route  라우트 이름
     * @param  string|null  $message  flash message (옵션)
     * @param  string  $type  flash message 키 (예: success, error)
     */
    protected function responseRedirect(
        string $route,
        ?string $message = null,
        string $type = 'success'
    ): RedirectResponse {
        $redirect = redirect()->route($route);

        if ($message) {
            $redirect->with($type, $message);
        }

        return $redirect;
    }

    /**
     * ⚠️ Redirect Back (이전 페이지로 이동)
     * 주로 validation 실패, 오류 알림 시 사용됩니다.
     *
     * @param  string|null  $message  flash message (옵션)
     * @param  string  $type  flash message 키 (예: error, warning)
     */
    protected function responseRedirectBack(
        ?string $message = null,
        string $type = 'error'
    ): RedirectResponse {
        $redirect = redirect()->back();

        if ($message) {
            $redirect->with($type, $message);
        }

        return $redirect;
    }

    /**
     * 🚫 404 Page (Not Found)
     *
     * @param  string  $message  에러 메시지
     */
    protected function responseNotFound(
        string $message = 'Page not found'
    ): View|Factory|Application {
        return view('errors.404', ['message' => $message]);
    }

    /**
     * 💥 500 Page (Internal Server Error)
     *
     * @param  string  $message  에러 메시지
     */
    protected function responseServerError(
        string $message = 'Something went wrong'
    ): View|Factory|Application {
        return view('errors.500', ['message' => $message]);
    }

    /**
     * 🔐 403 Page (Forbidden)
     *
     * @param  string  $message  에러 메시지
     */
    protected function responseForbidden(
        string $message = 'Access denied'
    ): View|Factory|Application {
        return view('errors.403', ['message' => $message]);
    }
}
