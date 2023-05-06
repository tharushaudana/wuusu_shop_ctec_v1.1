<?php

use App\Http\Controllers\web\ActivityController;
use App\Http\Controllers\web\AuthController;
use App\Http\Controllers\web\ProfileController;
use App\Http\Controllers\web\SessionController;
use App\Http\Controllers\web\UserController;
use App\Models\ApiSession;
use App\Models\User;
use App\Models\WebSession;
use OwenIt\Auditing\Models\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Bind values with route parameters
|--------------------------------------------------------------------------
*/

Route::bind('_web_session', function ($id) {
    return WebSession::find($id);
});

Route::bind('_api_session', function ($id) {
    return ApiSession::find($id);
});

Route::bind('_user', function ($id) {
    return User::find($id);
});

Route::bind('_audit', function ($id) {
    return Audit::find($id);
});

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/tt', function (Request $request) {
    return Auth::user()->audits()->paginate(2);
});

//### Login
Route::group(['prefix' => '/login'], function () {
    Route::get('/', [AuthController::class, 'login_Get'])->name('web.login');
    Route::post('/', [AuthController::class, 'login_Post'])->middleware('checkRecaptchaResponse');
});

Route::group(['middleware' => 'auth'], function () {
    //### Pages.Home
    Route::get('/', function () {
        return view('dashboard.pages.home');
    })->name('web.pages.home');

    //### Pages.My
    Route::group(['prefix' => '/my'], function () {
        Route::get('/', [ProfileController::class, 'index'])->name('web.pages.my');

        Route::group(['prefix' => '/sessions'], function () {
            Route::get('/', [SessionController::class, 'index'])->name('web.pages.my.sessions');
            Route::delete('/terminate/websession/{_web_session}', [SessionController::class, 'destroyWebSession'])->middleware('checkIsNull:_web_session', 'checkRecaptchaResponse');
            Route::delete('/terminate/apisession/{_api_session}', [SessionController::class, 'destroyApiSession'])->middleware('checkIsNull:_api_session', 'checkRecaptchaResponse');
        });

        Route::group(['prefix' => '/activities'], function() {
            Route::get('/', [ActivityController::class, 'index'])->name('web.pages.my.activities');
            Route::get('/info/{_audit}', [ActivityController::class, 'show'])->name('web.pages.my.activity')->middleware('checkIsNull:_audit');
            Route::post('/filter', [ActivityController::class, 'filter'])->middleware('checkRecaptchaResponse');
            Route::post('/download', [ActivityController::class, 'download'])->middleware('checkRecaptchaResponse');
        });
    });

    //### Pages.Users
    Route::group(['prefix' => '/users', 'middleware' => ['checkUserPrivilege:SHOW_USERS']], function () {
        Route::get('/', [UserController::class, 'index'])->name('web.pages.users');
        Route::post('/', [UserController::class, 'store'])->middleware('checkRecaptchaResponse', 'checkUserPrivilege:ADD_USERS');

        Route::group(['prefix' => '/{_user}', 'middleware' => ['checkIsNull:_user']], function() {
            Route::get('/', [UserController::class, 'show'])->name('web.pages.user');
            Route::group(['prefix' => '/activities', 'middleware' => ['checkUserPrivilege:SHOW_USER_ACTIVITIES']], function() {
                Route::get('/', [ActivityController::class, 'index'])->name('web.pages.user.activities');
                Route::get('/info/{_audit}', [ActivityController::class, 'show'])->name('web.pages.user.activity')->middleware('checkIsNull:_audit');
                Route::post('/filter', [ActivityController::class, 'filter'])->middleware('checkRecaptchaResponse');
                Route::post('/download', [ActivityController::class, 'download'])->middleware('checkRecaptchaResponse', 'checkUserPrivilege:DOWNLOAD_USER_ACTIVITIES');
            });
            Route::post('/setprivileges', [UserController::class, 'setPrivileges'])->middleware('checkUserPrivilege:UPDATE_USER_PRIVILEGES', 'checkRecaptchaResponse');
            Route::delete('/', [UserController::class, 'destroy'])->middleware('checkRecaptchaResponse', 'checkUserPrivilege:DELETE_USERS');
        });
    });

    //### Logout
    Route::get('/logout', [AuthController::class, 'logout'])->name('web.logout');
});
