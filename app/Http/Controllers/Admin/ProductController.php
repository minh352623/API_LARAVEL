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
                $uploadedFileUrl = cloudinary()->upload($request->file('file_path')->getRealPath())->getSecurePath();

                $dataInsert['file_path'] = $uploadedFileUrl;
            }
            $product = $product->create($dataInsert);

            //insert data to product_image
            if (!empty($request->image_detail)) {

                foreach ((array)($request->image_detail) as $fileName) {
                    $uploadedFileUrl = cloudinary()->upload(($fileName)->getRealPath())->getSecurePath();

                    $product->images()->create(
                        [
                            'image_path' => $uploadedFileUrl,

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
                    $uploadedFileUrl = cloudinary()->upload($request->file('file_path')->getRealPath())->getSecurePath();

                    $dataUpdate['file_path'] = $uploadedFileUrl;
                }
                $status = $product->update($dataUpdate);

                //insert data to product_image
                if (!empty($request->image_detail)) {
                    $product->images()->where('product_id', $product->id)->delete();
                    foreach ((array)($request->image_detail) as $fileName) {
                        $uploadedFileUrl = cloudinary()->upload(($fileName)->getRealPath())->getSecurePath();

                        $product->images()->create(
                            [
                                'image_path' => $uploadedFileUrl,

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
            $products = Product::orderBy('name', $request->order);
        } else {
            $products = Product::orderBy('name', 'asc');
        }

        if ($request->priceFilter) {
            $products = $products->orderBy('price', $request->priceFilter);
        } else {
            $products = $products->orderBy('price', 'asc');
        }

        if ($request->cate && count($request->cate) > 0) {

            $cate = $request->cate;
            $products =   $products->whereIn('category_id', $cate);
        }
        if ($request->price && count($request->price) > 0) {
            $price = $request->price;
            $products =   $products->whereBetween('price', $price);
        }
        $products = $products->paginate(9);
        foreach ($products as $item) {
            if ($item->images) {

                $item->image_Detail = $item->images;
            }
        }

        return $products;
    }

    function getMayLike(Request $request)
    {
        $idPro = $request->idPro;
        $cate = $request->cate;

        return Product::where('id', "<>", (int)$idPro)->where('category_id', $cate)->get();
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
