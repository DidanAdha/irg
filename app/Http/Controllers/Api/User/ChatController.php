<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Chatroom as Chat;
use App\Transaction as Trans;
use App\TransactionDetail as Detail;
use Auth;

class ChatController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function index(Request $request) {
        if ($request->tab == 1) {
            $chat = Chat::with('restaurants:id,name,phone_number,img', 'users:id,email' , 'transactions', 'transactions.transaction_details.menus:id,name,price,img')->
            where('users_id', Auth::user()->id)->whereHas('transactions', function($query) {
                return $query->where('is_done', 0)->whereIn('status', ['pending', 'process', 'decline']);
            })->get();
        } else {
            $chat = Chat::with('restaurants:id,name,phone_number,img', 'users:id,email' , 'reservations')->
            where('users_id', Auth::user()->id)->whereHas('reservations', function($query) {
                return $query->where('is_done', 0)->whereIn('status', ['pending', 'process', 'decline']);
            })->get();
        }

        return response([
            'chat' => $chat,
            'message' => 'Success',
            'status_code' => http_response_code()
        ]);
    }

    // public function declined() {

    // }

    // public function detail($id) {
    //     $trans = Detail::with('menus:id,name,price,img')->where('transactions_id', Auth::user()->id)->get();

    //     return response([
    //         'chat' => $chat,
    //         'message' => 'Success',
    //         'status_code' => http_response_code()
    //     ]);
    // }
}
