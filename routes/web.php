<?php

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DemandController as AdminDemandController;
use App\Http\Controllers\Admin\RegionController;


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

Auth::routes(['register' => false]);

Route::get('/', function () {
    if(\Illuminate\Support\Facades\Auth::user()->role->name == \App\Models\Role::ROLE_SUPERADMIN)
        return redirect('admin/dashboard');
    else
        return redirect('admin/admin/dashboard');
})->middleware('auth');

Route::prefix('admin')->middleware('auth')->group(function () {


    Route::middleware('superadmin')->group(function () {

        Route::prefix('category')->controller(CategoryController::class)->group(function () {
            Route::get('/', 'listing')->name('categories');
            Route::post('/', 'store');
            Route::post('/{category_id}', 'update');
            Route::delete('/{category_id}', 'destroy');

            Route::post('/validation/unique','isUnique');
        });

        Route::prefix('region')->controller(RegionController::class)->group(function () {
            Route::get('/', 'listing')->name('regions');
            Route::post('/', 'store');
            Route::post('/{region_id}', 'update');
            Route::delete('/{region_id}', 'destroy');
            Route::post('/validation/unique','isUnique');
        });

        Route::prefix('admin')->controller(AdminController::class)->group(function () {
            Route::get('/', 'listing')->name('admins');
            Route::post('/', 'store');
            Route::post('/{admin_id}', 'update');
            Route::delete('/{admin_id}', 'destroy');
            Route::post('/validation/unique','isUnique');

        });

        Route::prefix('/dashboard')->controller(DashboardController::class)->group(function () {
            Route::get('/', 'statistics')->name('index');

        });
    });

    Route::middleware('admin')->group(function () {
        Route::prefix('article')->controller(ArticleController::class)->group(function () {
            Route::get('/', 'listing')->name('articles');
            Route::post('/status/{article_id}', 'updateStatus');
            Route::delete('/delete/{article_id}','destroyArticle');
        });

        Route::prefix('demand')->controller(AdminDemandController::class)->group(function () {
            Route::get('/', 'listing')->name('demands');
            Route::get('/accept/{demand_id}', 'accept');
            Route::get('/refuse/{demand_id}', 'refuse');

        });
        Route::prefix('/admin/dashboard')->controller(DashboardController::class)->group(function () {
            Route::get('/', 'admin')->name('dashboardAdmin');

        });
        Route::prefix('/profile')->controller(AdminAuthController::class)->group(function () {
            Route::get('/', 'profile')->name('profile');
            Route::post('/update/{admin_id}','update');
            Route::post('/password/{admin_id}', 'changePassword');



        });

    });
});


// -------------------------------------



