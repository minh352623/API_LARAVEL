<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Groups;
use Illuminate\Http\Request;
use PHPUnit\TextUI\XmlConfiguration\Group;

class GroupController extends Controller
{
    //
    const PER_PAGE = 5;
    function index(Request $request)
    {
        if (empty($request->query('query')) || $request->query('query') == "") return  Groups::orderBy('created_at', 'desc')->paginate(self::PER_PAGE);

        $groups = Groups::where('name', "like", "%" . $request->query('query') . "%")->orderBy('created_at', 'desc')->paginate(self::PER_PAGE);
        return $groups;
    }
    function getAll()
    {
        return Groups::all();
    }
    function add(Request $request)
    {
        try {

            $group = new Groups();
            $group->name = $request->name;
            $group->save();

            return $group;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
    function group($group)
    {

        $groupInfo = Groups::find($group);
        if ($groupInfo) {
            return response()->json([
                'group' => $groupInfo
            ]);
        }
    }
    function update($group, Request $request)
    {

        try {
            $groupInfo = Groups::find($group);
            $groupInfo->name = $request->name;
            $groupInfo->save();
            return $groupInfo;
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ]);
        }
    }

    function delete($group)
    {
        $result = Groups::where('id', $group)->delete();
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
}
