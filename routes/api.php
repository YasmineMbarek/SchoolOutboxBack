<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DemandController as AdminDemandController ;
use App\Http\Controllers\Admin\RegionController;
use App\Http\Controllers\Customer\ArticleController;
use App\Http\Controllers\Customer\AuthController as CustomerAuthController;
use App\Http\Controllers\Customer\DemandController as CustomerDemandController;
use Illuminate\Support\Facades\Route;


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


Route::middleware('api')->group(function () {

    Route::prefix('admin')->group(function () {

        Route::controller(AdminAuthController::class)->group(function () {
            Route::post('login', 'login');

            Route::post('password/forgot',  'forgot');
            Route::post('password/reset', 'reset');

            Route::middleware('auth:admin')->group(function () {
                Route::post('profile', 'profile');
                Route::post('logout', 'logout');
                Route::post('refresh', 'refresh');
                Route::post('profile/{admin_id}','update');
                Route::post('/password/{admin_id}','changePassword');

            });
        });

        Route::middleware(['auth:admin', 'admin'])->group(function () {

            Route::prefix('demand')->controller(AdminDemandController::class)->group(function () {
                Route::get('/', 'index');
                Route::get('/accept/{demand_id}', 'accept');
                Route::get('/refuse/{demand_id}', 'refuse');
                Route::get('/{demand_id}', 'getDemand');
            });

        });

        Route::middleware([])->group(function () {

            Route::prefix('category')->controller(CategoryController::class)->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::post('/{category_id}', 'update');
                Route::delete('/{category_id}', 'destroy');
                Route::get('/{category_id}', 'getCategory');
            });

            Route::prefix('region')->controller(RegionController::class)->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::post('/{region_id}', 'update');
                Route::delete('/{region_id}', 'destroy');
                Route::get('/{region_id}', 'getRegion');
            });

            Route::prefix('admin')->controller(AdminController::class)->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'register');
                Route::post('/{admin_id}', 'update');
                Route::delete('/{admin_id}', 'destroy');
                Route::get('/{admin_id}', 'getAdmin');
            });
        });


    });

    Route::prefix('customer')->group(function () {

        Route::controller(CustomerAuthController::class)->group(function () {
            Route::post('register', 'register');
            Route::post('login', 'login');
            Route::post('password/forgot',  'forgot');
            Route::post('password/reset', 'reset');
            Route::get('regions', 'regions');
            Route::get('users', 'users');
            Route::get('categories', 'categories');
            Route::post('received/{article_id}', 'setArticleReceived');
            Route::post('affected/{article_id}', 'setArticleAffected');
            Route::get('home', 'articlesHome');
            Route::get('home/{article_id}', 'articleHome');






            Route::middleware('auth:customer')->group(function () {
                Route::post('profile', 'profile');
                Route::post('refresh', 'refresh');
                Route::post('logout', 'logout');
                Route::post('profile/post/picture/{customer_id}', 'storePicture');
                Route::delete('profile/delete/picture/{customer_id}', 'destroyPicture');

                Route::post('/{customer_id}','update');
               Route::post('/password/{customer_id}','changePassword');
            });
        });

        Route::middleware('auth:customer')->group(function () {

            Route::prefix('article')->controller(ArticleController::class)->group(function () {
                Route::get('/', 'index');
                Route::post('/store', 'store');
                Route::post('/picture/{article_id}', 'storePicture');
                Route::post('/{article_id}', 'update');
                Route::delete('/{article_id}', 'destroy');
                Route::delete('picture/{picture_id}', 'destroyPicture');
                Route::get('/{article_id}', 'getArticle');
            });

            Route::prefix('demand')->controller(CustomerDemandController::class)->group(function () {
                Route::get('/', 'index');
                Route::post('/{article_id}', 'create');
                Route::put('/{demand_id}', 'update');
                Route::delete('/{demand_id}', 'destroy');
            });
        });
    });
});



