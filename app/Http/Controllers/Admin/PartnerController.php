<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PartnerController extends Controller
{
    //
    const PER_PAGE = 3;
    function index(Request $request)
    {
        $sliders = Partner::orderBy('created_at', 'desc');
        if ($request->query('query') && $request->query('query') != "") {
            $keyword = $request->query('query');
            $sliders = $sliders->where(function ($query) use ($keyword) {
                $query->orWhere('name', 'like', '%' . $keyword . '%');
            });
        }
        return $sliders->paginate(self::PER_PAGE);
    }
    function getAll()
    {
        return Partner::all();
    }
    function add(Request $request)
    {
        try {
            $partner = new Partner();
            if ($request->hasFile('file_path')) {

                $partner->image = cloudinary()->upload($request->file('file_path')->getRealPath())->getSecurePath();
            }
            $partner->name = $request->name;

            $partner->save();
            return $partner;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    function delete($id)
    {
        $result = Partner::where('id', $id)->delete();
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

    function getPartner($id)
    {
        return Partner::find($id);
    }

    function update($id, Request $request)
    {
        try {
            $partner = Partner::find($id);
            if ($request->hasFile('file_path')) {

                $partner->image = cloudinary()->upload($request->file('file_path')->getRealPath())->getSecurePath();
            }
            $partner->name = $request->name;

            $partner->update();
            return $partner;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
