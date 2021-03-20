<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\SystemController;
use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\PostsController;
use App\Http\Controllers\Api\v1\SpecialtyController;
use App\Http\Controllers\Api\v1\SectionPostController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['as' => 'api.'], function () {
    /**
     * Api Test 용 컨트롤러.
     */
    Route::group(['prefix' => 'test', 'as' => 'test.'], function () {
        Route::post('index', [TestController::class, 'index'])->name('index');
        Route::post('slack', [TestController::class, 'slack'])->name('slack');
        Route::post('factory', [TestController::class, 'factory'])->name('factory');
        Route::post('base-user', [TestController::class, 'base_user'])->name('base.user');
    });

    Route::group(['prefix' => 'system', 'as' => 'system.'], function () {
        Route::get('check-status', [SystemController::class, 'checkStatus'])->name('check.status'); // 서버 체크
        Route::get('check-notice', [SystemController::class, 'checkNotice'])->name('check.notice'); // 서버 공지사항 체크
        Route::get('base-data', [SystemController::class, 'baseData'])->name('base.data');  //
    });

    /**
     * Api V1 Route Group.
     */
    // FIXME 2020-09-02 21:05 auth:api middleware 정책 수립?
    Route::group(['namespace' => 'v1', 'prefix' => 'v1', 'as' => 'v1.'], function () {
        Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
            Route::post('login', [AuthController::class, 'client_login'])->name('login');

            Route::post('logout', [AuthController::class, 'client_logout'])->name('logout')->middleware('auth:api');
            Route::get('login-check', [AuthController::class, 'client_login_check'])->name('logincheck')->middleware('auth:api');
            Route::post('token-refresh', [AuthController::class, 'client_token_refresh'])->name('token_refresh');
        });

        Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
        });

        Route::group(['prefix' => 'post', 'as' => 'post.'], function () {
            Route::get('/{page?}', [PostsController::class, 'index'])->name('index.paging');
            Route::post('/', [PostsController::class, 'create'])->name('create')->middleware('auth:api');
            Route::put('/{post_uuid}/publish', [PostsController::class, 'publish'])->name('publish')->middleware('auth:api');
            Route::put('/{post_uuid}/hide', [PostsController::class, 'hide'])->name('hide')->middleware('auth:api');
            Route::get('/{slug_title}/detail', [PostsController::class, 'detail'])->name('detail');
            Route::get('/{post_uuid}/edit', [PostsController::class, 'edit'])->name('edit')->middleware('auth:api');
            Route::put('/{post_uuid}/update', [PostsController::class, 'update'])->name('update')->middleware('auth:api');
            Route::put('/{post_uuid}/view-increment', [PostsController::class, 'view_increment'])->name('view.increment');
            Route::delete('/{post_uuid}/destroy', [PostsController::class, 'destroy'])->name('destroy')->middleware('auth:api');
            Route::post('/create-image', [PostsController::class, 'create_image'])->name('image.create')->middleware('auth:api');
            Route::get('/{search_item}/search', [PostsController::class, 'search'])->name('search');

            Route::get('/tag/tag-list', [PostsController::class, 'tag_list'])->name('tag_list');
            Route::get('/tag/{search_item}/tag-search', [PostsController::class, 'tag_search'])->name('tag_search');
            Route::get('/write/waiting-list', [PostsController::class, 'waiting_list'])->name('write.waiting.list')->middleware('auth:api');
        });

        Route::group(['prefix' => 'specialty', 'as' => 'specialty.'], function () {
            Route::get('/weather', [SpecialtyController::class, 'weather'])->name('weather');
            Route::get('/covid', [SpecialtyController::class, 'covid'])->name('covid');
        });

        Route::group(['prefix' => 'section-post', 'as' => 'section-post.'], function () {
            Route::get('/scribble', [SectionPostController::class, 'scribble_view'])->name('view.scribble'); // 끄적 끄적 글 정보 조회.
            Route::post('/scribble', [SectionPostController::class, 'scribble_create'])->name('create.scribble')->middleware('auth:api'); // 끄적 끄적 글 등록.
            Route::put('/scribble/{post_uuid}/view-increment', [SectionPostController::class, 'scribble_view_increment'])->name('increment.view'); // 끄적 끄적 뷰카운트.
            Route::get('/blog', [SectionPostController::class, 'blog_view'])->name('view.blogs'); // 불로그 소개 정보 조회.
            Route::post('/blog', [SectionPostController::class, 'blog_create'])->name('create.blogs')->middleware('auth:api'); // 불로그 소개 글 등록.
            Route::put('/blog/{post_uuid}/view-increment', [SectionPostController::class, 'blog_view_increment'])->name('increment.view'); // 블러그 뷰카운트.
            Route::get('/mingun', [SectionPostController::class, 'mingun_view'])->name('view.mingun'); // 민군은 글 보기.
            Route::post('/mingun', [SectionPostController::class, 'mingun_create'])->name('create.mingun')->middleware('auth:api'); // 민군은 글 등록.
            Route::put('/mingun/{post_uuid}/view-increment', [SectionPostController::class, 'mingun_view_increment'])->name('increment.view'); // 민군은 뷰카운트.
        });
    });
});




