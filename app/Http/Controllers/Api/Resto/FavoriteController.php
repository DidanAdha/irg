<?php

namespace App\Http\Controllers\Api\Resto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Menu;
use Auth;

class FavoriteController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    // public function index(Request $request) {
    //     if (Auth::user()->roles_id == 4) {
    //         $check = Auth::user()->restaurants->where('id', $request->resto)->first();
    //     } else {
    //         $id = $request->resto;
    //         $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
    //             return $query->where('restaurants_id', $id);
    //         })->find(Auth::user()->id);
    //     }

    //     if (isset($check)) {
    //         $id = $request->resto;
    //         if ($request->type == 0) {
    //             $menu = Menu::with('promos:id,in_percent,menus_id', 'menu_types:id,name')->where('restaurants_id', $request->resto)->where('is_favorite', 1)->get();
    //         } else {
    //             $menu = Menu::with('promos:id,in_percent,menus_id', 'menu_types:id,name')->where('restaurants_id', $request->resto)->where('menu_types_id', $request->type)->where('is_favorite', 1)->get();
    //         }
            
    //         return response([
    //             'menu' => $menu,
    //             'message' => 'Success',
    //             'status_code' => http_response_code()
    //         ]);
    //     } else {
    //         return response([
    //             'message' => 'Not Found',
    //             'status_code' => 404
    //         ], 404);
    //     }
    // }

    public function favorite(Request $request) {
        $menu = Menu::select('id', 'is_favorite', 'restaurants_id')->find($request->menus_id);
        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $menu->restaurants_id)->first();
        } else if (Auth::user()->roles_id == 5) {
            $id = $menu->restaurants_id;
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        } else {
            $check = null;
        }

        if (isset($check)) {
            if ($menu->is_favorite == 1) {
                $menu->is_favorite = 0;
            } else {
                $menu->is_favorite = 1;
            }
    
            $menu->save();
            
            return response([
                'message' => 'Success',
                'status_code' => http_response_code()
            ]);
        } else {
            return response([
                'message' => 'Not Found',
                'status_code' => 404
            ], 404);
        }
    }
}
