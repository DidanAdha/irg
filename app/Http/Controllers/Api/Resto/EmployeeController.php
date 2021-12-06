<?php

namespace App\Http\Controllers\Api\Resto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Employee;
use App\User;
use Auth;

class EmployeeController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function index(Request $request) {
        $resto = $request->resto;

        if ($resto == 0) {
            $employee = User::select('id', 'name', 'email', 'img', 'phone_number', 'employees_id', 'roles_id')->
                whereHas('employees', function($query){
                    return $query->where('owners_id', Auth::user()->id);
            })->get();
        } else {
            if (Auth::user()->roles_id == 4) {
                $check = Auth::user()->restaurants->where('id', $request->resto)->first();
            }

            if (isset($check)) {
                $employee = User::select('id', 'name', 'email', 'img', 'phone_number', 'employees_id', 'roles_id')->
                    whereHas('employees', function($query) use($resto) {
                        return $query->where('restaurants_id', $resto);
                })->get();
            } else {
                return response([
                    'message' => 'Not found',
                    'status_code' => 404
                ], 404);
            }
        }
        
        return response([
            'employee' => $employee,
            'message' => 'Success',
            'status_code' => http_response_code()
        ]);
    }

    public function add(Request $request) {
        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $request->restaurants_id)->first();
        }

        if (isset($check)) {
            $validated = $this->validate($request, [
                'name' => 'required|min:5|max:25',
                'email' => 'required|email|unique:users|max:50',
                'password' => 'required|min:8',
                'address' => 'required|max:100',
                'phone_number' => 'required'
            ]);
    
            $employee = new Employee;
            $employee->restaurants_id = $request->restaurants_id;
            $employee->owners_id = Auth::user()->id;
            $employee->save();
    
            $user = new User;
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->password = Hash::make($validated['password']);
            $user->address = $validated['address'];
            $user->phone_number = $validated['phone_number'];
            $user->roles_id = $request->roles_id;
            $user->employees_id = $employee->id;
            $user->priv_admin = 0;
            $user->save();
    
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

    public function edit(Request $request, $id) {
        $user = User::with('employees')->find($id);

        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('users_id', $user->employees->owners_id)->first();
        }

        if (isset($check)) {
            $validated = $this->validate($request, [
                'name' => 'required|min:5|max:25',
                'email' => 'sometimes|required|email|unique:users,email,'.$user->id
            ]);

            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->roles_id = $request->roles_id;
            $user->save();

            return response([
                'user' => $user,
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

    public function delete($id) {
        $user = User::with('employees')->find($id);
        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('users_id', $user->employees->owners_id)->first();
        }

        if (isset($check)) {
            Employee::find($user->employees_id)->delete();
            $user->delete();

            return response([
                'message' => 'Delete successful',
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
