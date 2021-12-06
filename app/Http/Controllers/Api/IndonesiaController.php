<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Indonesia;

class IndonesiaController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function city() {
        $city = Indonesia::allCities();
        return response([
            'city' => $city,
            'message' => 'Success',
            'status_code' => http_response_code()
        ]);
    }
}
