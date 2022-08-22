<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Traits\StorageImageTrait;

class ProductController extends Controller
{
    //
    // $product = new Product();
    // $product->name = $request->name;
    // $product->description = $request->description;
    // $product->price = $request->price;
    // if ($request->hasFile('file_path')) {

    //     $product->file_path = Storage::url($request->file('file_path')->store('public/product'));
    // }
    // $product->save();
    // return  $product;

    use StorageImageTrait;
    const PER_PAGE = 4;

    function index(Request $request)
    {
        if (empty($request->query('query')) || $request->query('query') == "") {

            $products = Product::orderBy('created_at', 'desc')->paginate(self::PER_PAGE);
            foreach ($products as $product) {
                if ($product->category->name) {

                    $product->category_name = $product->category->name;
                }
            }
            return $products;
        }

        $products = Product::where('name', "like", "%" . $request->query('query') . "%")->orderBY('created_at', 'desc')->paginate(self::PER_PAGE);

        foreach ($products as $product) {
            $product->category_name = $product->category->name;
        }
        return $products;
    }
    function addProduct(Request $request)
    {
        try {

            //code...



            //nếu validate thành công
            $product = new Product();
            $dataInsert = [

                'name' => $request->name,
                'price' => $request->price,
                'description' => $request->description,
                'category_id' => $request->category,
            ];
            if ($request->hasFile('file_path')) {

                $dataInsert['file_path'] = Storage::url($request->file('file_path')->store('public/product'));
            }
            $product = $product->create($dataInsert);

            //insert data to product_image
            if (!empty($request->image_detail)) {

                foreach ((array)($request->image_detail) as $fileName) {
                    $dataProductImageDetail = $this->storageTraitUploadMultiple($fileName, 'product');
                    $product->images()->create(
                        [
                            'image_path' => $dataProductImageDetail['file_path'],

                        ]
                    );
                }
            }

            return $product;
        } catch (\Exception $e) {
            //throw $th;
            DB::rollBack();
            Log::error($e->getMessage());
        }
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
                'detailImage' => $product->images
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
        $product = Product::find($id);
        if ($product) {
            try {
                //nếu validate thành công
                $dataUpdate = [

                    'name' => $request->name,
                    'price' => $request->price,
                    'description' => $request->description,
                    'category_id' => $request->category,
                ];
                if ($request->hasFile('file_path')) {

                    $dataUpdate['file_path'] = Storage::url($request->file('file_path')->store('public/product'));
                }
                $status = $product->update($dataUpdate);

                //insert data to product_image
                if (!empty($request->image_detail)) {
                    $product->images()->where('product_id', $product->id)->delete();
                    foreach ((array)($request->image_detail) as $fileName) {
                        $dataProductImageDetail = $this->storageTraitUploadMultiple($fileName, 'product');
                        $product->images()->create(
                            [
                                'image_path' => $dataProductImageDetail['file_path'],

                            ]
                        );
                    }
                }

                return $product;
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e->getMessage());
            }
        } else {
            return response()->json([
                'status' => 'error'
            ]);
        }
    }
}
