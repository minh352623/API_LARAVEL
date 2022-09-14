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
// Route::get('/chinh-sach-quyen-rieng-tu', function () {
//     return 'Chinh sach quyen rieng tu';
// });
// Route::get('/auth/facebook/callback', function () {
//     $user = Socialite::driver('facebook')->user();
//     $users = User::all();
//     foreach ($users as $item) {
//         if ($user->name == $item->name) {
//             $check = 1;
//             break;
//         } else {
//             $check  = 0;
//         }
//     }
//     if ($check == 1) {
//         $userNew = new User();
//         $userNew->name =  $user->name;
//         $userNew->email =  $user->email;
//         $userNew->image =  $user->avatar;
//         $userNew->password =  Hash::make('123456789');
//         $userNew->group_id =  3;
//     }

//     return $user;
// });
// Route::get('/auth/facebook', function () {
//     return Socialite::driver('facebook')->redirect();
// });
