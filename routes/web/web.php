<?php

use App\Http\Controllers\Web\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| 웹 애플리케이션용 라우트입니다.
| Blade 뷰, 폼, 페이지 전환 등을 처리합니다.
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

// Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
// Route::post('/login', [AuthController::class, 'login']);

// Route::middleware('auth')->group(function () {
//     Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
//     Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// });
