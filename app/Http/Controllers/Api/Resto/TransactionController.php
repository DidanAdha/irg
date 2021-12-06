<?php

namespace App\Http\Controllers\Api\Resto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Transaction as Trans;
use App\TransactionDetail as Detail;
use App\RestaurantTable as Table;
use App\Menu;
use Auth;
use App\User;

class TransactionController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function index(Request $request) {
        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $request->resto)->first();
        } else if (Auth::user()->roles_id == 5 || Auth::user()->roles_id == 6) {
            $id = $request->resto;
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        } else {
            $check = null;
        }

        if (isset($check)) {
            $trans = Trans::with('users:id,name,address,email,phone_number')->where('restaurants_id', $request->resto)->where('status', $request->status)->where('is_done', 0)->orderBy('created_at', 'desc')->get();
            return response([
                'trans' => $trans,
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
        $trans = Detail::with('menus:id,name,price,img,restaurants_id')->where('transactions_id', $id)->get();
        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $trans[0]->menus->restaurants_id)->first();
        } else {
            $id = $trans[0]->menus->restaurants_id;
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        }

        if (isset($check)) {
            return response([
                'trans' => $trans,
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

    public function editTransaction(Request $request, $id) {
        $trans = Detail::with('menus:id,name,price,img,restaurants_id', 'transactions:id,resto_edit')->where('id', $id)->first();
        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $trans->menus->restaurants_id)->first();
        } else if (Auth::user()->roles_id == 5 || Auth::user()->roles_id == 6) {
            $id = $trans->menus->restaurants_id;
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        } else {
            $check = null;
        }

        if (isset($check)) {
            if ($trans->transactions->resto_edit == 1) {
                $menu = Menu::with('promos')->find($trans->menus->id);
                $trans->menus_id = $request->menus_id;
                $trans->qty = $request->qty;
                $trans->price = $request->qty * $menu->price;
                if (isset($menu->promos)) {
                    $trans->discount = $menu->promo->discount * $request->qty;
                    $trans->discounted_price = $trans->price - $trans->discount;
                } else {
                    $trans->discount = 0;
                    $trans->discounted_price = $trans->price;
                }
                $trans->save();

                $detail = Detail::where('transactions_id', $trans->transactions->id)->get();
                $total_qty = $total_price = $total_discount = $discounted_price = 0;

                foreach ($detail as $i) {
                    $total_qty += $i->qty;
                    $total_price += $i->price;
                    $total_discount += $i->discount;
                    $discounted_price += $i->discounted_price;
                }

                $transaction = Trans::find($trans->transactions->id);
                $transaction->total_qty = $total_qty;
                $transaction->total_price = $total_price;
                $transaction->total_discount = $total_discount;
                $transaction->discounted_price = $discounted_price;
                $transaction->total_end = $discounted_price + $transaction->ongkir;
                $transaction->save();

                return response([
                    'message' => 'Success',
                    'status_code' => http_response_code()
                ]);
            } else {
                return response([
                    'message' => 'Oops',
                    'status_code' => http_response_code()
                ]);
            }
        } else {
            return response([
                'message' => 'Not Found',
                'status_code' => 404
            ], 404);
        }
    }

    public function acceptConfirm($id) {
        $trans = Trans::select('id', 'status', 'delivery', 'restaurant_tables_id', 'restaurants_id')->find($id);

        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $trans->restaurants_id)->first();
        } else if (Auth::user()->roles_id == 5 || Auth::user()->roles_id == 6) {
            $id = $trans->restaurants_id;
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        } else {
            $check = null;
        }

        if (isset($check)) {
            $trans->status = 'process';
            $trans->save();

            if ($trans->delivery == 0) {
                $table = Table::select('id')->find($trans->restaurant_tables_id);
                $table->status = 'filled';
                $table->save();
            }
            
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

    public function processConfirm($id) {
        $trans = Trans::select('id', 'status', 'delivery', 'restaurant_tables_id', 'restaurants_id', 'take_away')->find($id);

        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $trans->restaurants_id)->first();
        } else if (Auth::user()->roles_id == 5 || Auth::user()->roles_id == 6) {
            $id = $trans->restaurants_id;
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        } else {
            $check = null;
        }

        if (isset($check)) {
            if ($trans->delivery == 1 or $trans->take_away == 1) {
                $trans->status = 'finished';
                $trans->is_done = 1;
            } else {
                $trans->status = 'ready';
            }
    
            $trans->save();
            
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

    public function readyConfirm($id) {
        $trans = Trans::select('id', 'status', 'delivery', 'restaurant_tables_id', 'restaurants_id')->find($id);

        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $trans->restaurants_id)->first();
        } else if (Auth::user()->roles_id == 5 || Auth::user()->roles_id == 7) {
            $id = $trans->restaurants_id;
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        } else {
            $check = null;
        }

        if (isset($check)) {
            $trans->status = 'finished';
            $trans->is_done = 1;
            $trans->save();
            
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
    
    public function tableListPrice($id) {
        $trans = Trans::with('users:id,name', 'transaction_details', 'transaction_details.menus')->where('restaurant_tables_id', $id)->where('is_done', 0)->get();

        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $trans[0]->restaurants_id)->first();
        } else if (Auth::user()->roles_id == 5 || Auth::user()->roles_id == 7) {
            $id = $trans[0]->restaurants_id;
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        } else {
            $check = null;
        }

        if (isset($check)) {
            return response([
                'trans' => $trans,
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

    public function payTable($id) {
        $trans = Trans::select('id', 'status', 'delivery', 'restaurant_tables_id', 'restaurants_id')->where('restaurant_tables_id', $id)->where('is_done', 0)->get();

        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $trans[0]->restaurants_id)->first();
        } else if (Auth::user()->roles_id == 5 || Auth::user()->roles_id == 7) {
            $id = $trans[0]->restaurants_id;
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        } else {
            $check = null;
        }

        if (isset($check)) {

            foreach ($trans as $i) {
                $i->status = 'finished';
                $i->is_done = 1;
                $i->save();    
            }
            
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

    public function declineConfirm($id) {
        $trans = Trans::select('id', 'status', 'delivery', 'restaurant_tables_id', 'restaurants_id')->find($id);

        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $trans->restaurants_id)->first();
        } else if (Auth::user()->roles_id == 5 || Auth::user()->roles_id == 6) {
            $id = $trans->restaurants_id;
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        } else {
            $check = null;
        }

        if (isset($check)) {
            $trans->status = 'decline';
            $trans->is_done = 1;
            $trans->save();
            
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
