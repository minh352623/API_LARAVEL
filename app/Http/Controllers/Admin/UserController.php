<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Groups;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Socialite\Facades\Socialite;

// return $request->all();
// $user = User::where('email', $request->email)->first();
// if ($user && Hash::check($request->password, $user->password)) {
//     return response()->json([
//         "user" => $user
//     ]);
// } else {
//     return response()->json([
//         "error" => 'Nguòi dùng không tồn tại'
//     ]);
// }
// Auth::attempt(['email' => $request->email, 'password' => $request->password]);
// Auth::attempt($request->only('email,password'))
class UserController extends Controller
{
    //

    const PER_PAGE = 3;

    function index(Request $request)
    {
        $lists = DB::table('users')
            ->select('users.*', 'groups.name as name_group')
            ->join('groups', 'groups.id', '=', 'users.group_id');
        if ($request->keyword && $request->keyword !== "") {
            $keyword = $request->keyword;
            $lists = $lists->where(function ($query) use ($keyword) {
                $query->orWhere('users.name', 'like', '%' . $keyword . '%');
                $query->orWhere('users.email', 'like', '%' . $keyword . '%');
            });
        }
        if ($request->group && $request->group !== "") {
            $group = $request->group;
            $lists = $lists->where('group_id', $group);
        }
        $lists = $lists->orderBy('created_at', 'desc')->paginate(self::PER_PAGE);
        $groups = Groups::all();

        return response()->json([
            'users' => $lists,
            'groups' => $groups
        ]);
    }
    function delete($id)
    {
        $result = User::where('id', $id)->delete();
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

    function add(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
        ], [
            'email.required' => 'Email không được để trống !',
            'email.email' => 'Email không đúng định dạng !',
            'email.unique' => 'Email đã có người sử dụng !',
        ]);
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->group_id = $request->group;
        $user->password = Hash::make($request->password);
        if ($request->description) {
            $user->description = $request->description;
        }
        if ($request->hasFile('file_path')) {

            $user->image = Storage::url($request->file('file_path')->store('public/users'));
        }
        $user->save();
        return $user;
    }
    function getUser($id)
    {
        $user =  User::find($id);
        return $user;
    }
    function update($id, Request $request)
    {
        $user = User::find($id);
        if ($request->name) {
            $user->name = $request->name;
        }
        if ($request->description) {
            $user->description = $request->description;
        }
        if ($request->email) {
            $user->email = $request->email;
        }
        if ($request->group) {
            $user->group_id = $request->group;
        }
        if ($request->phone) {
            $user->phone = $request->phone;
        }
        if ($request->address) {
            $user->address = $request->address;
        }
        if ($request->password && $request->password != "") {
            $user->password = Hash::make($request->password);
        }
        if ($request->hasFile('file_path')) {

            $user->image = cloudinary()->upload($request->file('file_path')->getRealPath())->getSecurePath();
        }
        $user->save();
        $token = $request->user()->createToken('token')->plainTextToken;

        $user->token = $token;
        return $user;
    }
    //

    function register(Request $request)
    {
        $request->validate([
            "name" => 'required|min:8',
        ]);
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = '0123456789';

        $user->password = Hash::make($request->password);
        $user->group_id = 3;
        $user->save();
        return $user;
    }

    function login(Request $request)
    {
        try {
            if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                return response()->json([
                    'message' => 'Invalid credentials',
                    'status' => 401
                ], 401);
            }
            $user = Auth::user();
            $token = $request->user()->createToken('token')->plainTextToken;
            $cookie = cookie('jwt', $token, 60 * 24); //1 day
            $user->token = $token;

            if ($cookie) {
                return response([
                    'token' => $token,
                    'cookie' => $cookie,
                    'user' => $user,
                ])->withCookie($cookie);
            } else {
                return response([
                    'message' => "không có cookie"
                ]);
            }
        } catch (\Exception $e) {
            return response($e->getMessage());
        }
    }

    function user()
    {
        return Auth::user();
    }

    function logout()
    {
        $status =  DB::table('personal_access_tokens')->where('tokenable_id', "=", Auth::user()->id)->delete();
        $cookie = Cookie::forget('jwt');
        return response([
            'message' => 'success',
            "status" => $status
        ])->withCookie($cookie);
    }


    function redirecFace()
    {
        return Socialite::driver('facebook')->redirect();
    }

    // function callbackFacebook()
    // {
    //     $user = Socialite::driver('facebook')->user();
    //     $users = User::all();
    //     $check = 0;
    //     $userNew = null;
    //     foreach ($users as $item) {
    //         if ($user->name == $item->name) {
    //             $check = 1;
    //             $userNew = $item;

    //             break;
    //         } else {
    //             $check  = 0;
    //         }
    //     }
    //     if ($check == 0) {
    //         $userNew = new User();
    //         $userNew->name =  $user->getName();
    //         if ($user->getEmail()) {

    //             $userNew->email =  $user->getEmail();
    //         } else {
    //             $userNew->email =  'test@gmail.com';
    //         }
    //         $userNew->image =  $user->getAvatar();
    //         $userNew->password =  Hash::make('123456789');
    //         $userNew->group_id =  3;
    //         $userNew->phone = "0123456789";

    //         $userNew->save();
    //         return $userNew;
    //     } else {

    //         return $userNew;
    //     }
    // }
    function callbackFacebook(Request $request)
    {
        try {
            if (!Auth::attempt(['name' => $request->name])) {
                $userNew = new User();
                $userNew->name =  $request->name;

                $userNew->email =  'test@gmail.com';
                $userNew->image =  "";
                $userNew->password =  Hash::make('123456789');
                $userNew->group_id =  3;
                $userNew->phone = "0123456789";

                $userNew->save();
                $user = Auth::user();
                $token = $request->user()->createToken('token')->plainTextToken;
                $cookie = cookie('jwt', $token, 60 * 24); //1 day
                $user->token = $token;

                if ($cookie) {
                    return response([
                        'token' => $token,
                        'cookie' => $cookie,
                        'user' => $user,
                    ])->withCookie($cookie);
                } else {
                    return response([
                        'message' => "không có cookie"
                    ]);
                }
            }
            $user = Auth::user();
            $token = $request->user()->createToken('token')->plainTextToken;
            $cookie = cookie('jwt', $token, 60 * 24); //1 day
            $user->token = $token;

            if ($cookie) {
                return response([
                    'token' => $token,
                    'cookie' => $cookie,
                    'user' => $user,
                ])->withCookie($cookie);
            } else {
                return response([
                    'message' => "không có cookie"
                ]);
            }
        } catch (\Exception $e) {
            return response($e->getMessage());
        }
    }
}
