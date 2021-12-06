<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function index(Request $request) {
        if (Auth::user()->roles_id == 8) {
            $user = User::find(Auth::user()->id);
            $user->name = $request->name;
            $user->phone_number = $request->phone_number;
            $user->ttl = date("Y-m-d", strtotime($request->dob));
            $user->gender = $request->gender == '1' ? 'pria' : 'wanita';
            if (isset($request->old_password) and isset($request->new_password)) {
                if (Hash::make($request->old_password) == $user->password) {
                    if ($request->new_password == $request->confirm_password) {
                        $user->password = Hash::make($request->new_password);
                    }else{
                        return response([
                            'message' => 'Your password does not match', //need help
                            'status_code' => http_response_code()
                        ]);
                    }
                }else{
                    return response([
                        'message' => 'Invalid Password',
                        'status_code' => http_response_code()
                    ]);
                }
            }
            // $user->address = $request->address;
            $user->save();
            
            return response([
                'data' => $request,
                'message' => 'Success',
                'status_code' => http_response_code()
            ]);
        } else {
            return response([
                'message' => 'Not found',
                'status_code' => http_response_code()
            ], 404);
        }
    }
    public function detail($id)
    {
        return response([
            'data' => User::find($id),
            'message' => 'Success',
            'status_code' => http_response_code()
        ]);
    }
}
