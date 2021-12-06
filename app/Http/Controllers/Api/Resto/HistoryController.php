<?php

namespace App\Http\Controllers\Api\Resto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Transaction as Trans;
use App\TransactionDetail as Detail;
use Auth;
use App\User;
use App\Reservation;

class HistoryController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function index(Request $request) {
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
            if ($request->tab == 1) {
                $history = Trans::with('users:id,name,address,email,phone_number', 'chatrooms:id,transactions_id')->where('restaurants_id', $request->resto)->where('is_done', 1)->orderBy('created_at', 'desc')->get();
            } else {
                $history = Reservation::with('users:id,name,address,email,phone_number', 'chatrooms:id,reservations_id')->where('restaurants_id', $request->resto)->where('is_done', 1)->orderBy('created_at', 'desc')->get();
            }
            
            return response([
                'history' => $history,
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
        $history = Detail::with('menus:id,name,price,img,restaurants_id')->where('transactions_id', $id)->get();
        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $history[0]->menus->restaurants_id)->first();
        } else if (Auth::user()->roles_id == 5) {
            $id = $history[0]->menus->restaurants_id;
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        } else {
            $check = null;
        }

        if (isset($check)) {
            return response([
                'history' => $history,
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
    public function removeAll(){
        if (strtolower($request->type) == 'reservation') {
            $trans = Reservation::where('users_id', Auth::user()->id)->get();
        }elseif(strtolower($request->type) == 'order'){
            $trans = Trans::where('users_id', Auth::user()->id)->get();
        }else{
            return response([
                'message' => 'Success',
                'status_code' => http_response_code()
            ]);
        }
        foreach ($trans as $t) {
            $t->delete();
        }
        return response([
            'message' => 'Success',
            'status_code' => http_response_code()
        ]);
    }

    public function remove(Request $request){
        // 1,2,3 -> example array
        $id = explode(",", $request->id);
        foreach ($id as $i) {
            if (strtolower($request->type) == 'reservation') {
                $history = Reservation::find($i);
                $history->delete();
            }elseif(strtolower($request->type) == 'order'){
                $history = Trans::find($i);
                $history->delete();
            }else{
                return response([
                    'message' => 'Not Found',
                    'status_code' => 404
                ]);
            }
        }
        return response([
            'message' => 'Success',
            'status_code' => http_response_code()
        ]);
    }
}
