<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Menu;
use App\Bookmark;
use Auth;

class MenuController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function index(Request $request) {
        $id = $request->resto;

        if ($request->is_delivery == 1) {
            if ($request->type == 0) {
                $menu = Menu::with('promos:id,in_percent,menus_id', 'menu_types:id,name')->where('restaurants_id', $request->resto)->where('is_delivery', 1)->get();
            } else {
                $menu = Menu::with('promos:id,in_percent,menus_id', 'menu_types:id,name')->where('restaurants_id', $request->resto)->where('menu_types_id', $request->type)->where('is_delivery', 1)->get();
            }
        } else {
            if ($request->type == 0) {
                $menu = Menu::with('promos:id,in_percent,menus_id', 'menu_types:id,name')->where('restaurants_id', $request->resto)->get();
            } else {
                $menu = Menu::with('promos:id,in_percent,menus_id', 'menu_types:id,name')->where('restaurants_id', $request->resto)->where('menu_types_id', $request->type)->get();
            }
        }

        return response([
            'menu' => $menu,
            'message' => 'Success',
            'status_code' => http_response_code()
        ]);
    }

    public function detail($id) {
        $menu = Menu::with('promos', 'menu_types:id,name', 'restaurants:id,name')->where('id', $id)->first();
        $bookmark = Bookmark::where('menus_id', $id)->where('users_id', Auth::user()->id)->first();
        if (isset($bookmark)) {
            $bookmark = '1';
        } else {
            $bookmark = '0';
        }
        return response([
            'menu' => $menu,
            'bookmark' => $bookmark,
            'message' => 'Success',
            'status_code' => http_response_code()
        ]);
    }
}
