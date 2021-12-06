<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Cart;
use App\User;
use App\Menu;
use App\Promo;

class CartController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function index() {
        $cart = Cart::with('menus:id,name,price,delivery_price,img,restaurants_id', 'restaurants:id,latitude,longitude,ongkir')->where('users_id', Auth::user()->id)->get();
        $total_price = Auth::user()->carts()->sum('total_price');
        $total_discount = Auth::user()->carts()->sum('discount');
        $total_payment = Auth::user()->carts()->sum('discounted_price');
        return response([
            'cart' => $cart,
            'total_price' => $total_price,
            'total_discount' => $total_discount,
            'total_payment' => $total_payment,
            'message' => 'Success',
            'status_code' => http_response_code()
        ]);
    }

    public function add(Request $request) {
        $validated = $this->validate($request, [
            'qty' => 'required|numeric|min:1'
        ]);

        
        $discounted = 0;
        $promo = Promo::select('id', 'discount', 'delivery_discount')->where('menus_id', $request->menus_id)->first();
        $menu = Menu::select('id', 'price', 'delivery_price', 'is_delivery')->find($request->menus_id);
        if ($promo == null) {
            $discount = 0;
            $discounted = $request->total_price;
        } else {
            if ($request->delivery == 1) {
                $discount = $promo->delivery_discount * intval($validated['qty']); //
            } else {
                $discount = $promo->discount * intval($validated['qty']); //$validated['qty']
            }
            $discounted = $request->total_price - $discount;
        }
        // dd($discounted);

        $cart = new Cart;
        $cart->users_id = Auth::user()->id;
        $cart->menus_id = $request->menus_id;
        $cart->restaurants_id = $request->restaurants_id;
        $cart->qty = $validated['qty'];
        $cart->total_price = $request->total_price;
        $cart->discounted_price = $discounted;
        $cart->discount = $discount;
        $cart->delivery = $request->delivery;

        if (Auth::user()->in_cart == 0) {
            $cart->save();

            $user = User::select('id', 'in_cart')->find(Auth::user()->id);
            $user->in_cart = $cart->restaurants_id;
            $user->save();

            return response([
                'cart' => $cart,
                'message' => 'Success',
                'status_code' => http_response_code()
            ]);
        } else {
            if (Auth::user()->in_cart == $request->restaurants_id) {
                $cart->save();

                return response([
                    'cart' => $cart,
                    'message' => 'Success',
                    'status_code' => http_response_code()
                ]);
            } else {
                return response([
                    'message' => 'Please checkout your order with other restaurants first',
                    'status_code' => http_response_code()
                ]);
            }
        }
    }

    public function edit(Request $request, $id) {
        $validated = $this->validate($request, [
            'qty' => 'required|numeric|min:1',
            'total_price' => 'required|numeric|min:1',
        ]);

        
        $cart = Cart::find($id);
        $promo = Promo::select('id', 'discount')->where('menus_id', $cart->menus_id)->first();
        $menu = Menu::select('id', 'price', 'delivery_price')->find($cart->menus_id);

        $discount = $cart->discount;
        $discounted = $cart->discounted_price;
        if (isset($promo)) {
            if ($request->delivery == 1) {
                $discount = $promo->delivery_discount * $validated['qty'];
            } else {
                $discount = $promo->discount * $validated['qty'];
            }
            $discounted = $request->total_price - $discount;
        }else{
            $discounted = $validated['total_price'];
        }

        $cart->qty = $validated['qty'];
        $cart->total_price = $validated['total_price'];
        $cart->discount = $discount;
        $cart->discounted_price = $discounted;
        // $cart->note = $request->note;
        $cart->save();
        return response([
            'cart' => $cart,
            'message' => 'Success',
            'status_code' => http_response_code()
        ]);
    }

    public function delete($id = null) {
        if ($id != null and Cart::find($id)) {
            Cart::find($id)->delete();
            $cart = Cart::with('menus:id,name,price,img')->where('users_id', Auth::user()->id)->get();
            if (count($cart) == 0) {
                $user = User::select('id', 'in_cart')->find(Auth::user()->id);
                $user->in_cart = 0;
                $user->save();
            }
        }else{
            $cart = Cart::where('users_id', Auth::user()->id)->get();
            foreach ($cart as $i) {
                Cart::find($i->id)->delete();
            }
            
            $user = User::select('id', 'in_cart')->find(Auth::user()->id);
            $user->in_cart = 0;
            $user->save();
        }

        return response([
            'message' => 'Success',
            'status_code' => http_response_code()
        ]);
    } 

    public function deleteAll() {
        $cart = Cart::where('users_id', Auth::user()->id)->get();
        foreach ($cart as $i) {
            Cart::find($i->id)->delete();
        }
        
        $user = User::select('id', 'in_cart')->find(Auth::user()->id);
        $user->in_cart = 0;
        $user->save();

        return response([
            'message' => 'Success',
            'status_code' => http_response_code()
        ]);
    }
}
