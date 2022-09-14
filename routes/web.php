<?php

use App\Http\Controllers\Client\BillController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/test', function () {
    return view('welcome');
});

Route::get('/vnpay_return', [BillController::class, 'vnPay_return']);
Route::get('/chinh-sach-quyen-rieng-tu', function () {
    return 'Chinh sach quyen rieng tu';
});
Route::get('/auth/facebook/callback', function () {
    $user = Socialite::driver('facebook')->user();

    dd($user->name);
});
Route::get('/auth/facebook', function () {
    return Socialite::driver('facebook')->redirect();
});
