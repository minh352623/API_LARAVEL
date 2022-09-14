<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Session;

use Illuminate\Http\Request;

class CartController extends Controller
{
    //
    public function add(Request $request)
    {
        // $request->session()->forget('cart');
        $item = Product::find($request->id);
        $cartUser = Cart::where('id_user', $request->user_id)->first();
        if (empty($cartUser)) {
            $cartUser = new Cart();
            $cartUser->id_user = $request->user_id;
            $item->number = $request->number;
            $item->total = (int)($request->number) * (float)($item->price);
            $cart = [];
            array_push($cart, $item);
        } else if (empty(json_decode($cartUser->carts))) {
            $cartUser->id_user = $request->user_id;
            $item->number = $request->number;
            $item->total = (int)($request->number) * (float)($item->price);
            $cart = [];
            array_push($cart, $item);
        } else {

            $check = 0;
            $cart = json_decode($cartUser->carts);
            if (!empty($cart)) {
                $itemNew = null;
                $keyNew = null;
                foreach ($cart as $key => $itemChild) {

                    if ((int)($itemChild->id) === (int)($request->id)) {
                        $itemChild->number = (int)($itemChild->number) + (int)$request->number;
                        if ((int)($itemChild->number) <= 0) {
                            array_splice($cart, $key, 1);
                            $check = 2;
                            break;
                        }
                        $itemChild->total = (int)($itemChild->number) * (float)($item->price);

                        $itemNew = $itemChild;
                        $keyNew = $key;
                        $check = 1;
                        break;
                    }
                }
                if ($check === 1) {
                    $cart[$keyNew] = [];
                    $cart[$keyNew] = $itemNew;
                } else if ($check == 0) {
                    $item->number = $request->number;
                    $item->total = (int)($request->number) * $item->price;
                    array_push($cart, $item);
                }
            }
        }


        $cartUser->carts =  json_encode($cart);
        $cartUser->save();
        return $cartUser;
    }
    function all(Request $request)
    {
        return Cart::where('id_user', $request->id)->first();
    }
    public function delete(Request $request)
    {
        $cartUser = Cart::where('id_user', $request->user_id)->first();
        if (is_null($cartUser)) {
        } else {
            if (!empty($cartUser->carts)) {
                $cart = json_decode($cartUser->carts);
                $keyRemove = -1;
                foreach ($cart as $key => $item) {
                    if ((int)($item->id) === (int)($request->id_pro)) {
                        $keyRemove = $key;
                    }
                }
                if ($keyRemove > -1) {
                    array_splice($cart, $keyRemove, 1);
                }
            }
        }
        $cartUser->carts =  json_encode($cart);
        $cartUser->save();
        return $cartUser;
    }
}
