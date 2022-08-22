<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    //
    const PER_PAGE = 3;
    function index(Request $request)
    {
        $sliders = Slider::orderBy('created_at', 'desc');
        if ($request->query('query') && $request->query('query') != "") {
            $keyword = $request->query('query');
            $sliders = $sliders->where(function ($query) use ($keyword) {
                $query->orWhere('caption', 'like', '%' . $keyword . '%');
                $query->orWhere('heading', 'like', '%' . $keyword . '%');
                $query->orWhere('description', 'like', '%' . $keyword . '%');
            });
        }
        return $sliders->paginate(self::PER_PAGE);
    }
    function getAll()
    {
        return Slider::all();
    }
    function add(Request $request)
    {
        try {
            $slider = new Slider();
            if ($request->hasFile('file_path')) {

                $slider->file_path = Storage::url($request->file('file_path')->store('public/sliders'));
            }
            $slider->caption = $request->caption;
            $slider->heading = $request->heading;
            $slider->description = $request->description;

            $slider->save();
            return $slider;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    function delete($id)
    {
        $result = Slider::where('id', $id)->delete();
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

    function getSlider($id)
    {
        return Slider::find($id);
    }

    function update($id, Request $request)
    {
        try {
            $slider = Slider::find($id);
            if ($request->caption) {
                $slider->caption = $request->caption;
            }
            if ($request->heading) {
                $slider->heading = $request->heading;
            }
            if ($request->description) {
                $slider->description = $request->description;
            }
            if ($request->hasFile('file_path')) {

                $slider->file_path = Storage::url($request->file('file_path')->store('public/sliders'));
            }

            $slider->save();
            return $slider;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
