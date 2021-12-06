<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Facility;

class FacilityController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function index() {
        $type = Facility::select('id', 'name')->get();
        return response([
            'type' => $type,
            'message' => 'Success',
            'status_code' => http_response_code()
        ]);
    }
}
