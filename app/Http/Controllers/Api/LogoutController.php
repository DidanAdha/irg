<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\User;

class LogoutController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function index() {
        if (Auth::user()) {
            $device = User::select('id', 'device_id')->find(Auth::user()->id);
            $device->device_id = NULL;
            $device->save();
            
            $user = Auth::user()->token();
            $user->revoke();

            return response([
                'message' => 'Success',
                'status_code' => http_response_code()
            ]);
        } else {
            return response([
                'message' => 'Unable to logout',
                'status_code' => http_response_code()
            ]);
        }
    }
}
