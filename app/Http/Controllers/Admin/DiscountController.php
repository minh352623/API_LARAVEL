<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DiscountController extends Controller
{
    const PER_PAGE = 3;
    function index(Request $request)
    {
        $sliders = Discount::orderBy('created_at', 'desc');
        if ($request->query('query') && $request->query('query') != "") {
            $keyword = $request->query('query');
            $sliders = $sliders->where(function ($query) use ($keyword) {
                $query->orWhere('caption', 'like', '%' . $keyword . '%');
                $query->orWhere('title', 'like', '%' . $keyword . '%');
            });
        }
        return $sliders->paginate(self::PER_PAGE);
    }
    function getAll()
    {
        return Discount::all();
    }
    function add(Request $request)
    {
        try {
            $discount = new Discount();
            if ($request->hasFile('file_path')) {

                $discount->image = Storage::url($request->file('file_path')->store('public/discounts'));
            }
            $discount->caption = $request->caption;
            $discount->title = $request->title;


            $discount->save();
            return $discount;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    function delete($id)
    {
        $result = Discount::where('id', $id)->delete();
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

    function getDiscount($id)
    {
        return Discount::find($id);
    }

    function update($id, Request $request)
    {
        try {
            $discount = Discount::find($id);
            if ($request->hasFile('file_path')) {

                $discount->image = Storage::url($request->file('file_path')->store('public/partners'));
            }
            $discount->caption = $request->caption;
            $discount->title = $request->title;

            $discount->update();
            return $discount;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
