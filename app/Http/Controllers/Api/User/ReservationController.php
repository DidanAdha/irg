<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;
use App\Reservation;
use App\Chatroom;

class ReservationController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function store(Request $request) {
        $check = Hash::check($request->password, Auth::user()->password);
        
        if ($check == 1) {
            $reservation = new Reservation;
            $reservation->users_id = Auth::user()->id;
            $reservation->restaurants_id = $request->restaurants_id;
            $reservation->chair = $request->chair;
            $reservation->schedule = $request->date;
            $reservation->price = $request->price;
            $reservation->save();
            
            $chat = new Chatroom;
            $chat->users_id = Auth::user()->id;
            $chat->restaurants_id = $request->restaurants_id;
            $chat->reservations_id = $reservation->id;
            $chat->save();

            return response([
                'reservations' => $reservation,
                'id_chatroom' => $chat->id,
                'message' => 'Success',
                'status_code' => http_response_code()
            ]);
        } else {
            return response([
                'message' => 'Your password not valid',
                'status_code' => 422
            ]);
        }
    }
}
