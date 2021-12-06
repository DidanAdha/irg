<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Restaurant as Resto;
use App\Follow;
use App\Menu;
use Auth;

class RestoController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function near(Request $request) {
        $data = explode(',', base64_decode($request->hash));
        $latitude = $data[0];
        $longitude = $data[1];
        $now = date('H:i:s');
        $resto = Resto::selectRaw("*,
            round(6371 * acos(cos(radians($latitude)) 
            * cos(radians(restaurants.latitude)) 
            * cos(radians(restaurants.longitude) 
            - radians($longitude)) 
            + sin(radians($latitude)) 
            * sin(radians(restaurants.latitude))), 0) AS distance, if(scheduled = 1, 'close',
            if(open_at < close_at, (case when '$now' < close_at  and '$now' >= open_at THEN 'open' else 'close' end),
            (case when '$now' < close_at  and '$now' >= open_at THEN 'open' else 'close' end))) as now")->
        where('status', 'active')->orderBy('distance', 'ASC')->get();
        
        return response([
            'resto' => $resto,
            'message' => 'Success',
            'status_code' => http_response_code()
        ]);
    }

    // public function openNow(Request $request) {
    //     $data = explode(',', base64_decode($request->hash));
    //     $latitude = $data[0];
    //     $longitude = $data[1];
    //     $resto = Resto::selectRaw("*,
    //         round(6371 * acos(cos(radians($latitude)) 
    //         * cos(radians(restaurants.latitude)) 
    //         * cos(radians(restaurants.longitude) 
    //         - radians($longitude)) 
    //         + sin(radians($latitude)) 
    //         * sin(radians(restaurants.latitude))), 0) AS distance")->
    //     orderBy('distance', 'ASC')->where('close_at','>=',$request->now)->get();
        
    //     return response([
    //         'resto' => $resto,
    //         'message' => 'Success',
    //         'status_code' => http_response_code()
    //     ]);
    // }

    public function detail($id) {
        $resto = Resto::with('cuisines:cuisines.id,name', 'facilities:facilities.id,name', 'reservation_prices', 'images')->withCount('follows')->where('id', $id)->first();
        $follow = Follow::where('restaurants_id', $id)->where('users_id', Auth::user()->id)->first();
        $favorite = Menu::with('promos:id,in_percent,menus_id', 'menu_types:id,name')->where('restaurants_id', $id)->where('is_favorite', 1)->get();
        if (isset($follow)) {
            $follow = '1';
        } else {
            $follow = '0';
        }
        $f = count(Follow::where('restaurants_id', $id)->get());
        if ($f > 1000000) $follower = strval($f/1000000).'M';
        elseif($f > 1000) $follower = strval($f/1000).'K';
        else $follower = $f;
        return response([
            'resto' => $resto,
            'follow' => $follow,
            'follower' => $follower,
            'favorite' => $favorite,
            'message' => 'Success',
            'status_code' => http_response_code()
        ]);
    }
}
