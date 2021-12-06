<?php

namespace App\Http\Controllers\Api\Resto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;
use App\User;

class AuthController extends Controller
{
    public function register(Request $request) {
        $validated = $this->validate($request, [
            'name' => 'required|min:5|max:25',
            'email' => 'required|email|max:50',
            'password' => 'required|min:8',
            // 'address' => 'required|max:100',
            'phone_number' => 'required',
            // 'gender'  => 'required',
            // 'dob'  => 'required'
        ]);
        
        
        if (!User::where('email', '=', $request->email)->exists()) {
            $user = new User;
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->password = Hash::make($validated['password']);
            // $user->address = $validated['address'];
            $user->phone_number = $validated['phone_number'];
            $user->roles_id = 4;
            $user->priv_admin = 0;
            // $user->ttl = date("Y-m-d", strtotime($validated['dob']));
            // $user->gender = $validated['gender'] == 1 ? 'pria' : 'wanita';
            $user->save();

            // $accessToken = $user->createToken('authToken')->accessToken;
            return response([
                'user' => $user,
                'message' => 'Registration successful',
                'status_code' => http_response_code()
            ]);

        }else{
            return response([
                'message' =>  'Duplicated email',
                'status_code' => 400
            ]);
        }
    }

    public function login(Request $request) {
        $validated = $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);
        
        if (!Auth::attempt($validated)) {
            return response([
                'message' => 'Your email or password is not valid',
                'status_code' => 422
            ]);
        }

        if (Auth::user()->roles_id >= 4 && Auth::user()->roles_id < 8) {
            $accessToken = Auth::user()->createToken('authToken')->accessToken;
            $user = User::find(Auth::user()->id);
            $user->device_id = $request->device_id;
            $user->save();
            return response([
                'user' => Auth::user(),
                'employee' => Auth::user()->employees,
                'access_token' => $accessToken,
                'message' => 'success',
                'status_code' => http_response_code()
            ]);
        } else {
            return response([
                'message' => 'your email or password is not valid',
                'status_code' => 422
            ]);
        }
    }
}
