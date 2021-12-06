<?php

namespace App\Http\Controllers\Api\Resto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Hash;
use App\User;

class UserController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }
    
    public function index(Request $request) {
        $owner = User::find(Auth::user()->id);
        $owner->name = $request->name;
        $owner->email = $request->email;
        $owner->phone_number = $request->phone_number;
        // $owner->ttl = date("Y-m-d", strtotime($request->dob));
        // $owner->gender = $request->gender == '1' ? 'pria' : 'wanita';
        if (isset($request->old_password) and isset($request->new_password)) {
            if (Hash::check($request->old_password, $owner->password)) {
                if ($request->new_password == $request->confirm_password) {
                    $owner->password = Hash::make($request->new_password);
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
        $owner->save();
        
        return response([
            'data' => $request,
            'message' => 'Success',
            'status_code' => http_response_code()
        ]);
    }
}
