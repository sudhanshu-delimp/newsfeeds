<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RssController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [UserController::class, 'login'])->name('admin_login');
Route::post('process_login', [UserController::class, 'processLogin'])->name('process_login');

Route::group([
    'middleware' => 'isLogin'
    ],
    function() {
        Route::get('logout', [UserController::class, 'logout'])->name('logout');
        Route::get('getFeedsContent', [RssController::class, 'getFeedsContent']);
        Route::resource('manage_user', UserController::class);
        Route::resource('manage_post', PostController::class);
        Route::resource('manage_site', SiteController::class);
        Route::resource('manage_feed', FeedController::class);
    }
  );