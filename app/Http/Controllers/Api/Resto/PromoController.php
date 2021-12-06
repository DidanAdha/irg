<?php

namespace App\Http\Controllers\Api\Resto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Promo;
use App\Menu;
use Auth;
use App\User;
use App\Follow;
use Carbon\Carbon;
use App\Jobs\PromoExpired;

class PromoController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function index(Request $request) {
        $id = $request->resto;
        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $id)->first();
        } else {
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        }

        if (isset($check)) {
            $promo = Menu::with('promos:id,in_percent,menus_id', 'menu_types:id,name')->
                whereHas('promos', function($query) use($id) {
                    return $query->where('restaurants_id', $id)->where('is_active', 1);
            })->get();

            return response([
                'promo' => $promo,
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
        
        

        $menu = Menu::select('id', 'price', 'delivery_price', 'restaurants_id', 'is_delivery')->find($request->menus_id);

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
            $validated = $this->validate($request, [
                'desc' => 'required',
                'in_percent' => 'required|numeric|min:10|max:100',
            ]);
    
            $promo = Promo::select('id', 'menus_id')->where('menus_id', $request->menus_id)->first();
    
            if (isset($promo)) {
                return response([
                    'message' => 'This menu has an active promo',
                    'status_code' => http_response_code()
                ]);
            }

            $discount = ($validated['in_percent'] / 100) * $menu->price;
            $delivery_discount = ($validated['in_percent'] / 100) * $menu->delivery_price;
            
    
            $promo = new Promo;
            $promo->menus_id = $request->menus_id;
            $promo->restaurants_id = $menu->restaurants_id;
            $promo->desc = $validated['desc'];
            $promo->in_percent = $validated['in_percent'];
            $promo->discount = $discount;
            $promo->delivery_discount = $delivery_discount;
            $promo->expired_at = $request->expired_at;
            $promo->save();

            // $expired = (New PromoExpired($promo->id))->delay(Carbon::now()->addMinutes(1));
            // $expired = (New PromoExpired($promo->id));
            $follower = [];
            foreach (Follow::where('restaurants_id', $menu->restaurants_id)->get() as $follows) {
                $follower[] = $follows->users->device_id;
            }
            return response([
                // 'expired' => $expired,
                'follower' => $follower,
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
        $promo = Promo::with('menus:id,price,delivery_price')->find($id);
        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $promo->restaurants_id)->first();
        } else if (Auth::user()->roles_id == 5) {
            $id = $promo->restaurants_id;
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        } else {
            $check = null;
        }

        if (isset($check)) {
            $validated = $this->validate($request, [
                'desc' => 'required',
                'in_percent' => 'required|numeric|min:10|max:100',
            ]);
    
            $discount = ($validated['in_percent'] / 100) * $promo->menus->price;
            $delivery_discount = ($validated['in_percent'] / 100) * $promo->menus->delivery_drice;
    
            $promo->desc = $validated['desc'];
            $promo->in_percent = $validated['in_percent'];
            $promo->discount = $discount;
            $promo->delivery_discount = $delivery_discount;
            $promo->expired_at = $request->expired_at;
            $promo->save();
    
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

    public function delete($id) {
        $promo = Promo::find($id);

        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $promo->restaurants_id)->first();
        } else if (Auth::user()->roles_id == 5) {
            $id = $promo->restaurants_id;
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        } else {
            $check = null;
        }

        if (isset($check)) {
            $promo->delete();
            return response([
                'message' => 'Delete Successful',
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
