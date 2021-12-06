<?php

namespace App\Http\Controllers\Api\Resto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Reservation;
use Auth;
use App\User;

class ReservationController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function index(Request $request) {
        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $request->resto)->first();
        } else if (Auth::user()->roles_id == 5 || Auth::user()->roles_id == 7) {
            $id = $request->resto;
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        } else {
            $check = null;
        }

        if (isset($check)) {
            $reservation = Reservation::with('users:id,name,address,email,phone_number')->where('restaurants_id', $request->resto)->where('status', $request->status)->where('is_done', 0)->get();
            return response([
                'reservation' => $reservation,
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

    public function acceptConfirm($id) {
        $reservation = Reservation::select('id', 'status', 'restaurants_id')->find($id);

        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $reservation->restaurants_id)->first();
        } else if (Auth::user()->roles_id == 5 || Auth::user()->roles_id == 7) {
            $id = $reservation->restaurants_id;
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        } else {
            $check = null;
        }

        if (isset($check)) {
            $reservation->status = 'process';
            $reservation->save();

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
        $reservation = Reservation::select('id', 'status', 'restaurants_id')->find($id);

        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $reservation->restaurants_id)->first();
        } else if (Auth::user()->roles_id == 5 || Auth::user()->roles_id == 6) {
            $id = $reservation->restaurants_id;
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        } else {
            $check = null;
        }

        if (isset($check)) {
            $reservation->status = 'finished';
            $reservation->is_done = 1;
            $reservation->save();
            
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
        $reservation = Reservation::select('id', 'status', 'restaurants_id')->find($id);

        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $reservation->restaurants_id)->first();
        } else if (Auth::user()->roles_id == 5 || Auth::user()->roles_id == 6) {
            $id = $reservation->restaurants_id;
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        } else {
            $check = null;
        }

        if (isset($check)) {
            $reservation->status = 'decline';
            $reservation->is_done = 1;
            $reservation->save();
            
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
