<?php

namespace App\Http\Controllers\Api\Resto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use File;
use ImageOptimizer;
use App\Menu;
use Auth;
use App\User;
use App\Promo;

class MenuController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function index(Request $request) {
        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $request->resto)->first();
        } else {
            $id = $request->resto;
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        }

        // return $check;

        if (isset($check)) {
            $id = $request->resto;
            // if ($request->is_delivery == 1) {
            //     if ($request->type == 0) {
            //         $menu = Menu::with('promos:id,in_percent,menus_id', 'menu_types:id,name')->where('restaurants_id', $request->resto)->where('is_favorite', 1)->where('is_delivery', 1)->get();
            //     } else {
            //         $menu = Menu::with('promos:id,in_percent,menus_id', 'menu_types:id,name')->where('restaurants_id', $request->resto)->where('menu_types_id', $request->type)->where('is_delivery', 1)->get();
            //     }
            // } else {
                if ($request->type == 0) {
                    $menu = Menu::with('promos:id,in_percent,menus_id,expired_at', 'menu_types:id,name')->where('restaurants_id', $request->resto)->where('is_favorite', 1)->get();
                } else {
                    $menu = Menu::with('promos:id,in_percent,menus_id,expired_at', 'menu_types:id,name')->where('restaurants_id', $request->resto)->where('menu_types_id', $request->type)->get();
                }
            // }
            
            return response([
                'menu' => $menu,
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

    public function detail($id) {
        $menu = Menu::with('promos', 'menu_types:id,name', 'restaurants:id,name')->where('id', $id)->first();

        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $menu->restaurants_id)->first();
        } else {
            $id = $menu->restaurants_id;
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        }

        if (isset($check)) {
            return response([
                'menu' => $menu,
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

    public function add(Request $request) {
        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $request->restaurants_id)->first();
        } else if (Auth::user()->roles_id == 5) {
            $id = $request->restaurants_id;
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        } else {
            $check = null;
        }

        if (isset($check)) {
            $name_img = '/storage/menu_img/default.jpg';
            $validated = $this->validate($request, [
                'name' => 'required',
                'desc' => 'required',
                'price' => 'required|numeric|min:1'
            ]);

            $data = $request->img_encode;
            list($type, $data) = explode(';', $data);
            list(, $type) = explode('/', $type);
            list(, $data) = explode(',', $data);
            $data = base64_decode($data);

            if (isset($data)) {
                $name_img = '/'.'storage/menu_img/img'.$request->restaurants_id.date('Ymdhis').'.'.$type;
                $location = '/'.'menu_img/img'.$request->restaurants_id.date('Ymdhis').'.'.$type;
                Storage::disk('public')->put($location, $data);
                ImageOptimizer::optimize(public_path($name_img));
            }

            $menu = new Menu;
            $menu->restaurants_id = $request->restaurants_id;
            $menu->name = $validated['name'];
            $menu->desc = $validated['desc'];
            $menu->price = $validated['price'];
            $menu->menu_types_id = $request->menu_types_id;
            $menu->img = $name_img;

            if ($request->delivery_price != 0) {
                $menu->delivery_price = $request->delivery_price;
                $menu->is_delivery = 1;
            }

            $menu->save();

            return response([
                'menu' => $menu,
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

    public function edit(Request $request, $id) {
        $menu = Menu::with('promos:id,in_percent,menus_id')->find($id);
        // return $menu->promos->id;
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
            $name_img = $menu->img;
            $validated = $this->validate($request, [
                'name' => 'required',
                'desc' => 'required',
                'price' => 'required|numeric|min:1'
            ]);

            $data = $request->img_encode;

            if ($data != 'null') {
                list($type, $data) = explode(';', $data);
                list(, $type) = explode('/', $type);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);

                if (isset($data)) {
                    $name_img = '/'.'storage/menu_img/img'.$menu->restaurants_id.date('Ymdhis').'.'.$type;
                    $location = '/'.'menu_img/img'.$menu->restaurants_id.date('Ymdhis').'.'.$type;
                    Storage::disk('public')->put($location, $data);
                    if (File::exists(public_path($menu->img))) {
                        if ($menu->img != '/storage/menu_img/default.jpg') {
                            File::delete(public_path($menu->img));
                        }
                    }
                    ImageOptimizer::optimize(public_path($name_img));
                }
            }

            $menu->name = $validated['name'];
            $menu->desc = $validated['desc'];
            $menu->price = $validated['price'];
            $menu->menu_types_id = $request->menu_types_id;
            $menu->img = $name_img;
            $menu->is_ready = $request->is_ready;
            $menu->delivery_price = $request->delivery_price;

            if ($request->delivery_price == 0) {    
                $menu->is_delivery = 0;
            } else {
                $menu->is_delivery = 1;
            }

            $menu->save();

            if (isset($menu->promos)) {
                $promo = Promo::find($menu->promos->id);
                $discount = ($promo->in_percent / 100) * $menu->price;
                $promo->discount = $discount;
                $promo->save();
            }

            return response([
                'menu' => $menu,
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

    public function delete($id) {
        $menu = Menu::find($id);

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
            if (File::exists(public_path($menu->img))) {
                if ($menu->img != '/storage/menu_img/default.jpg') {
                    File::delete(public_path($menu->img));
                }
            }
    
            $menu->delete();
    
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
