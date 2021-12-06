<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Restaurant as Resto;
use App\Schedules;
use App\Menu;
use App\Promo;

class PromoController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function index(Request $request) {
        if ($request->tab == 1) {
            $promo = Menu::with('promos:id,in_percent,menus_id', 'menu_types:id,name')->whereHas('promos', function($query){
                return $query->where('is_active', 1);
            })->get();
        } else {
            $promo = Menu::with('promos:id,in_percent,menus_id', 'menu_types:id,name')->whereHas('promos', function($query){
                return $query->where('is_active', 1);
            })->where('is_delivery', 1)->get();
        }

        foreach ($promo as $p) {
        	$resto = Resto::find($p->restaurants_id);
        	if ($resto->scheduled == 1) {
        		$s = Schedules::where('restaurants_id', $p->restaurants_id)->orderBy('expired_at', 'desc')->first();
        		$begin = $s->begin_at;
        		$expire = $s->expired_at;
        	}else{
        		$begin = $resto->open_at;
        		$expire = $resto->close_at;
        	}
        	//
        	if (
        		date('H:i:s') > date('H:i:s', strtotime($begin)) &&
        		date('H:i:s') < date('H:i:s', strtotime($expire))
        	) {
        		$p->now = "open";
        	}else {
        		$p->now = "close";
        	}
        }

        return response([
            'promo' => $promo,
            'message' => 'Success',
            'status_code' => http_response_code()
        ]);
    }
}
