<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Cuisine;

class CuisineController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function index() {
        $cuisine = Cuisine::select('id', 'name')->get();
        return response([
            'cuisine' => $cuisine,
            'message' => 'Success',
            'status_code' => http_response_code()
        ]);
    }
}
