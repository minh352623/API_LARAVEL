<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SliderController;


use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', [UserController::class, 'user']);
    Route::get('/logout', [UserController::class, 'logout']);
    Route::prefix('product')->name('product')->group(function () {
        Route::get('list', [ProductController::class, 'index']);

        Route::post('add', [ProductController::class, 'addProduct']);

        Route::delete('delete/{id}', [ProductController::class, 'delete']);

        Route::get('/{id}', [ProductController::class, 'update']);

        Route::post('/{id}', [ProductController::class, 'postUpdate']);
    });

    Route::prefix('groups')->name('groups')->group(function () {
        Route::get('/list', [GroupController::class, 'index']);
        Route::get('/all', [GroupController::class, 'getAll']);
        Route::get('update/{group}', [GroupController::class, 'group']);

        Route::post('update/{group}', [GroupController::class, 'update']);

        Route::post('/add', [GroupController::class, 'add']);
        Route::delete('delete/{id}', [GroupController::class, 'delete']);
    });
    Route::prefix('users')->name('users')->group(function () {
        Route::get('/list', [UserController::class, 'index']);
        Route::delete('/delete/{id}', [UserController::class, 'delete']);
        Route::post('/add', [UserController::class, 'add']);
        Route::get('update/{id}', [UserController::class, 'getUser']);
        Route::post('update/{id}', [UserController::class, 'update']);
    });

    Route::prefix('category')->name('category')->group(function () {
        Route::get('/all', [CategoryController::class, 'getAll']);

        Route::get('/list', [CategoryController::class, 'index']);
        Route::post('/add', [CategoryController::class, 'add']);
        Route::get('update/{id}', [CategoryController::class, 'getCategory']);
        Route::post('update/{id}', [CategoryController::class, 'update']);
        Route::delete('/delete/{id}', [CategoryController::class, 'delete']);
    });
    Route::prefix('slider')->name('slider')->group(function () {
        Route::get('/list', [SliderController::class, 'index']);
        Route::get('/all', [SliderController::class, 'getAll']);

        Route::post('/add', [SliderController::class, 'add']);

        Route::get('update/{id}', [SliderController::class, 'getSlider']);

        Route::post('update/{id}', [SliderController::class, 'update']);


        Route::delete('/delete/{id}', [SliderController::class, 'delete']);
    });

    Route::prefix('setting')->name('setting')->group(function () {
        Route::get('/list', [SettingController::class, 'index']);
        Route::get('/all', [SettingController::class, 'getAll']);

        Route::post('/add', [SettingController::class, 'add']);
        Route::get('update/{id}', [SettingController::class, 'getSetting']);
        Route::post('update/{id}', [SettingController::class, 'update']);
        Route::delete('/delete/{id}', [SettingController::class, 'delete']);
    });
    Route::prefix('menu')->name('menu')->group(function () {
        Route::get('/list', [MenuController::class, 'index']);
        Route::get('/recusive', [MenuController::class, 'recusive']);
        Route::post('/add', [MenuController::class, 'add']);
        Route::get('/all', [MenuController::class, 'getAll']);
        Route::delete('/delete/{id}', [MenuController::class, 'delete']);
        Route::get('update/{id}', [MenuController::class, 'getMenu']);
        Route::post('update/{id}', [MenuController::class, 'update']);
    });
});


Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
