<?php

namespace App\Http\Controllers\Api\Resto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Restaurant;
use Auth;
use App\Chatroom as Chat;
use App\User;

class ChatController extends Controller
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
            if ($request->tab == 1) {
                $chat = Chat::with('users:id,name,email,img,phone_number', 'restaurants:id', 'transactions:id,status', 'transactions.transaction_details.menus:id,name,price,img')->
                    where('restaurants_id', $request->resto)->whereHas('transactions', function($query) {
                        return $query->where('is_done', 0)->whereIn('status', ['process']);
                })->get();
            } else {
                $chat = Chat::with('users:id,name,email,img,phone_number', 'restaurants:id', 'reservations')->
                    where('restaurants_id', $request->resto)->whereHas('reservations', function($query) {
                        return $query->where('is_done', 0)->whereIn('status', ['process']);
                })->get();
            }
            
            return response([
                'chat' => $chat,
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
