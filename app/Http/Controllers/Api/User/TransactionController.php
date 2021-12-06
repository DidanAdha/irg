<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;
use App\Transaction as Trans;
use App\TransactionDetail as Detail;
use App\RestaurantTable as Table;
use App\Cart;
use App\User;
use App\Chatroom;
use App\Follow;

class TransactionController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function index() {
        $trans = Trans::with('restaurants:id,name,img')->where('users_id', Auth::user()->id)->where('is_done', 0)->where('delivery', 0)->whereIn('status', ['pending', 'process', 'ready'])->orderBy('created_at', 'desc')->get();
        return response([
            'trans' => $trans,
            'message' => 'Success',
            'status_code' => http_response_code()
        ]);
    }

    public function detail($id) {
        $trans = Detail::with('menus:id,name,price,img', 'transactions')->where('transactions_id', $id)->get();
        return response([
            'trans' => $trans,
            'message' => 'Success',
            'status_code' => http_response_code()
        ]);
    }

    public function restoEdit($id) {
        $trans = Trans::select('id', 'resto_edit')->where('users_id', Auth::user()->id)->where('id', $id)->first();
        $trans->resto_edit = 1;
        $trans->save();
        return response([
            'message' => 'Success',
            'status_code' => http_response_code()
        ]);
    }

    public function checkout(Request $request) {
        $check = Hash::check($request->password, Auth::user()->password);
        $table_id = 0;
        
        if ($check == 1) {
            $cart = Cart::where('users_id', Auth::user()->id)->get();
            $total_qty = $total_price = $total_discount = $discounted_price = 0;

            foreach ($cart as $i) {
                $total_qty += $i->qty;
                $total_price += $i->total_price;
                $total_discount += $i->discount;
                $discounted_price += $i->discounted_price;
            }
            
            if ($request->delivery == 0 && $request->barcode != "") {
                $table = Table::where('restaurants_id', $cart[0]->restaurants_id)->where('barcode', $request->table)->first();
                if (isset($table)) {
                    // if ($table->status != 'empty') {
                    //     return response([
                    //         'message' => 'The table you selected is already filled',
                    //         'status_code' => http_response_code()
                    //     ]);    
                    // }
                    $table_id = $table->id;
                } else {
                    return response([
                        'message' => 'Your QrCode is not valid',
                        'status_code' => http_response_code()
                    ]);
                }
            }

            //hitung ongkir
            

            $trans = new Trans;
            $trans->users_id = Auth::user()->id;
            $trans->restaurants_id = $cart[0]->restaurants_id;
            $trans->total_qty = $total_qty;
            $trans->total_price = $total_price;
            $trans->total_discount = $total_discount;
            $trans->discounted_price = $discounted_price;
            $trans->restaurant_tables_id = $table_id;
            // $trans->promos_id = $request->promos_id;
            $trans->delivery = $request->delivery;
            $trans->take_away = $request->take_away;
            $trans->address = $request->address;
            $trans->ongkir = $request->delivery == 1  ? $request->ongkir : 0;
            $trans->total_end = $discounted_price + $trans->ongkir;
            $trans->save();

            foreach ($cart as $i) {
                $detail = new Detail;
                $detail->transactions_id = $trans->id;
                $detail->menus_id = $i->menus_id;
                $detail->qty = $i->qty;
                $detail->price = $i->total_price;
                $detail->discount = $i->discount;
                $detail->discounted_price = $i->discounted_price;
                $detail->save();
                Cart::find($i->id)->delete();
            }

            $id_chat = 0;
            if ($request->delivery == 1 or $request->take_away == 1) {
                $chatroom = new Chatroom;
                $chatroom->users_id = Auth::user()->id;
                $chatroom->restaurants_id = $cart[0]->restaurants_id;
                $chatroom->transactions_id = $trans->id;
                $chatroom->save();
                $id_chat = $chatroom->id;
            }

            $user = User::select('id', 'in_cart')->find(Auth::user()->id);
            $user->in_cart = 0;
            $user->save();

            $follow = new Follow;
            $follow->users_id = Auth::user()->id;
            $follow->restaurants_id = $cart[0]->restaurants_id;
            $follow->save();

            $id = $cart[0]->restaurants_id;
            $employee = User::select('id', 'employees_id', 'device_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->get();

            return response([
                'trans' => $trans,
                'id_chatroom' => $id_chat,
                'employee' => $employee,
                'message' => 'Success',
                'status_code' => http_response_code()
            ]);
        } else {
            return response([
                'message' => 'Your password is not valid',
                'status_code' => 422
            ]);
        }
    }
}
