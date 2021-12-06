<?php

namespace App\Http\Controllers\Api\Resto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Schedule;
use App\User;
use Auth;

class ScheduleController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function store(Request $request) {
        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $request->resto)->first();
        } else {
            $id = $request->resto;
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        }

        if (isset($check)) {
            $schedule = new Schedule;
            $schedule->restaurants_id = $request->resto;
            $schedule->begin_at = $request->begin_at;
            $schedule->expired_at = $request->expired_at;
            $schedule->save();

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
