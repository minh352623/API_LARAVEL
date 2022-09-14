<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Client\BillController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\CommentController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

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
        Route::get('/list', [ProductController::class, 'index']);
        Route::get('/top10', [ProductController::class, 'top10']);
        Route::get('/bestSaler', [ProductController::class, 'getNewTenProduct']);

        Route::get('/getNew', [ProductController::class, 'getNewTenProduct']);


        Route::post('/add', [ProductController::class, 'addProduct']);
        Route::post('/getMayLike', [ProductController::class, 'getMayLike']);

        Route::delete('delete/{id}', [ProductController::class, 'delete']);

        Route::post('/filter', [ProductController::class, 'getProductFilter']);

        Route::get('/update/{id}', [ProductController::class, 'update']);

        Route::post('/update/{id}', [ProductController::class, 'postUpdate']);
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

        Route::get('/getCateTk', [CategoryController::class, 'getCateTk']);
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
    Route::prefix('partner')->name('partner')->group(function () {
        Route::get('/list', [PartnerController::class, 'index']);
        Route::get('/all', [PartnerController::class, 'getAll']);

        Route::post('/add', [PartnerController::class, 'add']);
        Route::get('update/{id}', [PartnerController::class, 'getPartner']);
        Route::post('update/{id}', [PartnerController::class, 'update']);
        Route::delete('/delete/{id}', [PartnerController::class, 'delete']);
    });
    Route::prefix('discount')->name('discount')->group(function () {
        Route::get('/list', [DiscountController::class, 'index']);
        Route::get('/all', [DiscountController::class, 'getAll']);

        Route::post('/add', [DiscountController::class, 'add']);
        Route::get('update/{id}', [DiscountController::class, 'getDiscount']);
        Route::post('update/{id}', [DiscountController::class, 'update']);
        Route::delete('/delete/{id}', [DiscountController::class, 'delete']);
    });
    Route::prefix('cart')->name('cart')->group(function () {

        Route::post('/add', [CartController::class, 'add']);
        Route::get('/all/{id}', [CartController::class, 'all']);

        Route::post('/delete', [CartController::class, 'delete']);
    });
    Route::prefix('bill')->name('bill')->group(function () {
        Route::get('/billTk', [BillController::class, 'getBillMonth']);

        Route::post('/add', [BillController::class, 'add']);
        Route::get('/detail/{id}', [BillController::class, 'detail']);
        Route::post('/list', [BillController::class, 'list']);
        Route::get('/listBillAdmin', [BillController::class, 'listBillAdmin']);
        Route::post('/update/{id}', [BillController::class, 'update']);
        Route::post('/vnPay', [BillController::class, 'vnPay']);
    });
    Route::prefix('comment')->name('comment')->group(function () {
        Route::get('/get/{product}', [CommentController::class, 'get']);
        Route::get('/getAll', [CommentController::class, 'getAll']);
        Route::delete('/delete/{id}', [CommentController::class, 'delete']);

        Route::post('/add', [CommentController::class, 'add']);
        Route::post('/caculatorComment', [CommentController::class, 'caculatorComment']);
    });
});


Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::get('caculator', [ProductController::class, 'caculator']);


Route::group(['middleware' => ['web']], function () {
    // your routes here
    Route::get('/chinh-sach-quyen-rieng-tu', function () {
        return 'Chinh sach quyen rieng tu';
    });
    Route::get('/auth/facebook/callback', function () {
        $user = Socialite::driver('facebook')->user();
        $users = User::all();
        foreach ($users as $item) {
            if ($user->name == $item->name) {
                $check = 1;
                break;
            } else {
                $check  = 0;
            }
        }
        if ($check == 1) {
            $userNew = new User();
            $userNew->name =  $user->name;
            $userNew->email =  $user->email;
            $userNew->image =  $user->avatar;
            $userNew->password =  Hash::make('123456789');
            $userNew->group_id =  3;
        }

        return $user;
    });
    Route::get('/auth/facebook', function () {
        return Socialite::driver('facebook')->redirect();
    });
});
