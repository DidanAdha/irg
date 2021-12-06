<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index(){
    	echo '<pre>';
    	print_r(auth()->user()->createToken('authToken')->accessToken);
    	echo '</pre>';
    }
}