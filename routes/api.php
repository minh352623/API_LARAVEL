<?php

use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);


Route::prefix('product')->name('product')->group(function () {
    Route::get('list', [ProductController::class, 'index']);

    Route::post('add', [ProductController::class, 'addProduct']);

    Route::delete('delete/{id}', [ProductController::class, 'delete']);

    Route::get('/{id}', [ProductController::class, 'update']);

    Route::post('/{id}', [ProductController::class, 'postUpdate']);
});
