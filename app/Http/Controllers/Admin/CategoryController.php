<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    //
    function index(Request $request)
    {
        $category = Category::orderBy('created_at', 'desc');
        if ($request->query('query') && $request->query('query') != "") {
            $keyword = $request->query('query');
            $category = $category->where('name', 'like', '%' . $keyword . '%');
        }
        return $category->paginate(3);
    }

    function add(Request $request)
    {
        $category = new Category();
        $category->name = $request->name;

        if ($request->hasFile('file_path')) {

            $category->image = Storage::url($request->file('file_path')->store('public/category'));
        }
        if ($request->hasFile('icon_image')) {

            $category->icon_image = Storage::url($request->file('icon_image')->store('public/category'));
        }
        $category->save();
        return $category;
    }
    function getAll()
    {
        return Category::all();
    }
    function getCategory($id)
    {
        return Category::find($id);
    }

    function update($id, Request $request)
    {
        try {
            $category = Category::find($id);
            if ($request->name) {
                $category->name = $request->name;
            }
            if ($request->hasFile('file_path')) {

                $category->image = Storage::url($request->file('file_path')->store('public/category'));
            }
            if ($request->hasFile('icon_image')) {

                $category->icon_image = Storage::url($request->file('icon_image')->store('public/category'));
            }
            $category->save();
            return $category;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    function delete($id)
    {
        $result = Category::where('id', $id)->delete();
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

    //thống kê

    function getCateTk()
    {
        $lists = DB::table('categories')
            ->selectRaw('categories.name ,count(products.id) as pro_count')
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->groupBy('categories.id', 'categories.name')
            ->get();
        return $lists;
    }
}
