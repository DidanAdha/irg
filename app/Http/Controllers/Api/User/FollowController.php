<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Follow;
use App\Restaurant as Resto;
use Auth;

class FollowController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function index(Request $request) {    
        $data = explode(',', base64_decode($request->hash));
        $latitude = $data[0];
        $longitude = $data[1];
        $resto = Resto::selectRaw("*,
        round(6371 * acos(cos(radians($latitude)) 
        * cos(radians(restaurants.latitude)) 
        * cos(radians(restaurants.longitude) 
        - radians($longitude)) 
        + sin(radians($latitude)) 
        * sin(radians(restaurants.latitude))), 0) AS distance, if(open_at < close_at, 
        (case when '$request->now' < close_at  and '$request->now' >= open_at THEN 'open' else 'close' end),
        (case when '$request->now' > close_at  and '$request->now' >= open_at THEN 'open' else 'close' end)) as now")->
        whereHas('follows', function($query){
            return $query->where('users_id', Auth::user()->id);
        })->where('status', 'active')->orderBy('distance', 'ASC')->get();

        return response([
            'resto' => $resto,
            'message' => 'Success',
            'status_code' => http_response_code()
        ]);
    }

    public function follow(Request $request) {
        $followed = Follow::where('restaurants_id', $request->restaurants_id)->where('users_id', Auth::user()->id)->first();
        
        if (isset($followed)) {
            Follow::find($followed->id)->delete();
        } else {
            $follow = new Follow;
            $follow->users_id = Auth::user()->id;
            $follow->restaurants_id = $request->restaurants_id;
            $follow->save();
        }
        
        return response([
            'message' => 'Success',
            'status_code' => http_response_code()
        ]);
    }

    public function unfollow($id) {

    }
}
