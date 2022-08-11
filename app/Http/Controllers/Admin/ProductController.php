<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    //

    const PER_PAGE = 4;
    function index(Request $request)
    {
        if (empty($request->query('query')) || $request->query('query') == "") return  Product::orderBy('created_at', 'desc')->paginate(self::PER_PAGE);

        $products = Product::where('name', "like", "%" . $request->query('query') . "%")->orderBY('created_at', 'desc')->paginate(self::PER_PAGE);
        return $products;
    }
    function addProduct(Request $request)
    {

        $product = new Product();
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        if ($request->hasFile('file_path')) {

            $product->file_path = Storage::url($request->file('file_path')->store('public/product'));
        }
        $product->save();
        return  $product;
    }

    function delete($id)
    {
        $result = Product::where('id', $id)->delete();
        if ($result) {
            return response()->json(
                [
                    'status' => 'sucesss',
                    'data' => $result,
                ]
            );
        } else {
            return response()->json([
                'status' => 'error'
            ]);
        }
    }

    function update($id, Request $request)
    {
        $product =  Product::find($id);
        if ($product) {
            return response()->json([
                'data' => $product,
                'status' => "success",
            ]);
        } else {
            return response()->json([
                'status' => "error",
            ]);
        }
        // return $request->input();
    }
    function postUpdate($id, Request $request)
    {
        // return $request->input();
        $product = Product::find($id);
        if ($product) {
            $product->name = $request->name;
            $product->description = $request->description;
            $product->price = $request->price;
            if ($request->hasFile('file_path')) {
                $product->file_path = Storage::url($request->file('file_path')->store('public/product'));
            }
            $product->update();
            return response()->json([
                'status' => 'success',
                'data' => $product
            ]);
        } else {
            return response()->json([
                'status' => 'error'
            ]);
        }
    }
}
