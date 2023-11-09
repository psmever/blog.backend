<?php

use App\Http\Controllers\Web\TestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
	return view('pages.landing', ['showEnvironment' => true]);
});

Route::group(['prefix' => 'web', 'as' => 'web.'], function () {
	Route::group(['prefix' => 'test', 'as' => 'test.'], function () {
		Route::controller(TestController::class)->group(function () {
			Route::get('/default', 'default')->name('default');
		});
	});
});

