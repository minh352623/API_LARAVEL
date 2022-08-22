<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    //
    const PER_PAGE = 4;

    function index(Request $request)
    {
        $settings = Setting::orderBy('created_at', 'desc');
        if ($request->query('query') && $request->query('query') != "") {
            $keyword = $request->query('query');
            $settings = $settings->where(function ($query) use ($keyword) {
                $query->orWhere('config_key', 'like', '%' . $keyword . '%');
                $query->orWhere('config_value', 'like', '%' . $keyword . '%');
            });
        }
        return $settings->paginate(self::PER_PAGE);
    }
    function getAll()
    {
        return Setting::all();
    }
    function add(Request $request)
    {
        $setting = new Setting();
        $setting->config_key = $request->config_key;
        $setting->config_value = $request->config_value;
        $setting->save();
        return $setting;
    }
    function delete($id)
    {
        $result = Setting::where('id', $id)->delete();
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

    function getSetting($id)
    {
        return Setting::find($id);
    }

    function update($id, Request $request)
    {
        try {
            $setting = Setting::find($id);
            if ($request->config_key) {
                $setting->config_key = $request->config_key;
            }
            if ($request->config_value) {
                $setting->config_value = $request->config_value;
            }


            $setting->save();
            return $setting;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
