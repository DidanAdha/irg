<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Transaction as Trans;
use App\TransactionDetail as Detail;
use App\Reservation;

class HistoryController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function index(Request $request) {
        if ($request->tab == 1) {
            $history = Trans::with('restaurants:id,name', 'chatrooms:id,transactions_id')->where('users_id', Auth::user()->id)->where('is_done', 1)->orderBy('created_at', 'desc')->get();
        } else {
            $history = Reservation::with('restaurants:id,name', 'chatrooms:id,reservations_id')->where('users_id', Auth::user()->id)->where('is_done', 1)->orderBy('created_at', 'desc')->get();
        }

        return response([
            'history' => $history,
            'message' => 'Success',
            'status_code' => http_response_code()
        ]);
    }

    public function detail($id) {
        $history = Detail::with('menus:id,name,price,img')->where('transactions_id', $id)->get();
        return response([
            'history' => $history,
            'message' => 'Success',
            'status_code' => http_response_code()
        ]);
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
            }elseif(strtolower($request->type) == 'order'){
                $history = Trans::find($i);
            }else{
                return response([
                    'message' => 'Success',
                    'status_code' => http_response_code()
                ]);
            }
            $history->delete();
        }
        return response([
            'message' => 'Success',
            'status_code' => http_response_code()
        ]);
    }
}
