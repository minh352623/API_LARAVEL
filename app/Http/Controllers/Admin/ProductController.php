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

    function top10()
    {
        return Product::orderBy('view', 'desc')->limit(5)->get();
    }
    function bestSaler()
    {
        $news =  Product::orderBy('created_at', 'desc')->limit(10)->get();

        foreach ($news as $new) {
            if ($new->images) {

                $new->imageDetail = $new->images;
            }
        }
        return $news;
    }
    function getNewTenProduct()
    {
        $news =  Product::orderBy('created_at', 'desc')->limit(10)->get();

        foreach ($news as $new) {
            if ($new->images) {

                $new->imageDetail = $new->images;
            }
        }
        return $news;
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
        $product->cate_name = $product->category;
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

    function getProductFilter(Request $request)
    {
        if ($request->order) {
            $product = Product::orderBy('name', $request->order);
        } else {
            $product = Product::orderBy('name', 'asc');
        }

        if ($request->priceFilter) {
            $product = Product::orderBy('price', $request->priceFilter);
        } else {
            $product = Product::orderBy('price', 'asc');
        }

        if ($request->cate && count($request->cate) > 0) {

            $cate = $request->cate;
            $product =   $product->whereIn('category_id', $cate);
        }
        if ($request->price && count($request->price) > 0) {
            $price = $request->price;
            $product =   $product->whereBetween('price', $price);
        }
        $product = $product->paginate(9);
        foreach ($product as $item) {
            if ($item->images) {

                $item->image_Detail = $item->images;
            }
        }

        return $product;
    }

    function getMayLike(Request $request)
    {
        $idPro = $request->idPro;
        $cate = $request->cate;

        return Product::where('id', "<>", $idPro)->where('category_id', $cate)->get();
    }
    function caculator()
    {
        $products = Product::all();
        foreach ($products as $product) {
            $commentList = $product->comments;


            if (count($commentList) > 0) {
                $rating = 0;

                foreach ($commentList as $item) {
                    $rating += (int)$item->rating;
                }
                $rating = number_format((float)($rating / (count($commentList))), 1, '.', '');
                $product->start = $rating;
                $product->update();
            }
        }


        return $products;
    }
}
