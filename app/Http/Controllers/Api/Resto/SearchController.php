<?php

namespace App\Http\Controllers\Api\Resto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Restaurant as Resto;
use App\Menu;
use Auth;

class SearchController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function resto(Request $request) {
        if (Auth::user()->roles_id == 4) {
            $resto = Resto::where('name', 'like', '%'.$request->name.'%')->where('users_id', Auth::user()->id)->get();
            return response([
                'resto' => $resto,
                'message' => 'Success',
                'status_code' => http_response_code()
            ]);
        } else {
            return response([
                'message' => 'Not found',
                'status_code' => http_response_code()
            ], 404);
        }
    }

    public function menu(Request $request) {
        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $request->resto)->first();
        } else if (Auth::user()->roles_id == 5) {
            $id = $request->resto;
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        } else {
            $check = null;
        }

        if (isset($check)) {
            if ($request->start != '' && $request->end != '') {
                $search = Menu::with('promos:id,in_percent,menus_id', 'menu_types:id,name')->
                where([
                    ['name', 'like', '%'.$request->name.'%'],
                    ['menu_types_id', 'like', '%'.$request->type.'%']
                ])->whereBetween('price', [$request->start, $request->end])->where('restaurants_id', $request->resto)->get();
            } else {
                $search = Menu::with('promos:id,in_percent,menus_id', 'menu_types:id,name')->
                where([
                    ['name', 'like', '%'.$request->name.'%'],
                    ['menu_types_id', 'like', '%'.$request->type.'%']
                ])->where('restaurants_id', $request->resto)->get();
            }

            return response([
                'search' => $search,
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
