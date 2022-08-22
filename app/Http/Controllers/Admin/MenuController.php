<?php

namespace App\Http\Controllers\Admin;

use App\Components\MenuRecusive;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    //
    const PER_PAGE = 5;
    public function __construct(MenuRecusive $menuRecusive)
    {
        $this->menuRecusive = $menuRecusive;
    }
    function index(Request $request)
    {
        $settings = Menu::orderBy('created_at', 'desc');
        if ($request->query('query') && $request->query('query') != "") {
            $keyword = $request->query('query');
            $settings = $settings->where(function ($query) use ($keyword) {
                $query->orWhere('name', 'like', '%' . $keyword . '%');
            });
        }
        return $settings->paginate(self::PER_PAGE);
    }
    function getAll()
    {
        return Menu::all();
    }

    function delete($id)
    {
        $result = Menu::where('id', $id)->delete();
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

    function recusive()
    {
        return response()->json([
            'data' => $this->menuRecusive->menuRecusiveAdd('')
        ]);
    }

    function add(Request $request)
    {

        try {
            $menu = new Menu();
            $menu->name = $request->name;
            $menu->parent_id = $request->parent_id;
            $menu->save();
            return $menu;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }


    function getMenu($id)
    {

        $menu = Menu::find($id);
        $parent = $this->menuRecusive->menuRecusiveAdd('', $menu->id);
        return response()->json([
            'result' => $menu,
            'parent' => $parent
        ]);
    }

    function update($id, Request $request)
    {
        try {
            $menu = Menu::find($id);
            $menu->name = $request->name;
            $menu->parent_id = $request->parent_id;


            $menu->update();
            return $menu;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
