<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Role;

class RoleController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    // public function index() {
    //     $role = Role::select('id', 'name')->whereIn('id', [3,4,5])->get();
    //     return response([
    //         'role' => $role,
    //         'message' => 'Success',
    //         'status_code' => http_response_code()
    //     ]);
    // }
}
