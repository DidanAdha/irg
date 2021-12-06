<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Restaurant as Resto;
use App\Menu;

class SearchController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function index(Request $request) {
        if ($request->tab == 1) {
            $data = explode(',', base64_decode($request->hash));
            $latitude = $data[0];
            $longitude = $data[1];
            
            $whereArray = [
                ['name', 'like', '%'.$request->name.'%'],
                ['cities_id', 'like', '%'.$request->city.'%'],
                ['status', 'active']
            ];

            $selectRaw = "*,
            round(6371 * acos(cos(radians($latitude)) 
            * cos(radians(restaurants.latitude)) 
            * cos(radians(restaurants.longitude) 
            - radians($longitude)) 
            + sin(radians($latitude)) 
            * sin(radians(restaurants.latitude))), 0) AS distance, if(scheduled = 1, 'close',
            if(open_at < close_at, (case when '$request->now' < close_at  and '$request->now' >= open_at THEN 'open' else 'close' end),
            (case when '$request->now' > close_at  and '$request->now' >= open_at THEN 'open' else 'close' end))) as now";

            if ($request->cuisine != '' && $request->facility == '') {
                $cuisine = explode(',', $request->cuisine);
                if (count($cuisine) == 1) {
                    $cuisine = [$request->cuisine];
                }
                $search = Resto::selectRaw($selectRaw)->where($whereArray)->
                    whereHas('cuisines', function($query) use($cuisine) {
                        return $query->whereIn('cuisine_id', $cuisine);
                })->orderBy('distance', 'ASC')->get();
            } else if ($request->cuisine == '' && $request->facility != '') {
                $facility = explode(',', $request->facility);
                if (count($facility) == 1) {
                    $facility = [$request->facility];
                }
                $search = Resto::selectRaw($selectRaw)->where($whereArray)->
                    whereHas('facilities', function($query) use($facility) {
                        return $query->whereIn('facility_id', $facility);
                })->orderBy('distance', 'ASC')->get();
            } else if ($request->cuisine != '' && $request->facility != '') {
                $cuisine = explode(',', $request->cuisine);
                if (count($cuisine) == 1) {
                    $cuisine = [$request->cuisine];
                }

                $facility = explode(',', $request->facility);
                if (count($facility) == 1) {
                    $facility = [$request->facility];
                }

                $cuisine = explode(',', $request->cuisine);
                $search = Resto::selectRaw($selectRaw)->where($whereArray)->
                    whereHas('facilities', function($query) use($facility) {
                        return $query->whereIn('facility_id', $facility);
                })->whereHas('cuisines', function($query) use($cuisine) {
                        return $query->whereIn('cuisine_id', $cuisine);
                })->orderBy('distance', 'ASC')->get();
            } else {
                $search = Resto::selectRaw($selectRaw)->where($whereArray)->orderBy('distance', 'ASC')->get();
            }
        } else if ($request->tab == 2) {
            if ($request->start != '' && $request->end != '') {
                if ($request->delivery == 1) {
                    $search = Menu::with('promos:id,in_percent,menus_id', 'menu_types:id,name')->
                    where([
                        ['name', 'like', '%'.$request->name.'%'],
                        ['menu_types_id', 'like', '%'.$request->type.'%'],
                        ['is_delivery', '=', 1]
                    ])->whereBetween('price', [$request->start, $request->end])->get();
                } else {
                    $search = Menu::with('promos:id,in_percent,menus_id', 'menu_types:id,name')->
                    where([
                        ['name', 'like', '%'.$request->name.'%'],
                        ['menu_types_id', 'like', '%'.$request->type.'%']
                    ])->whereBetween('price', [$request->start, $request->end])->get();
                }
            } else {
                if ($request->delivery == 1) {
                    $search = Menu::with('promos:id,in_percent,menus_id', 'menu_types:id,name')->
                    where([
                        ['name', 'like', '%'.$request->name.'%'],
                        ['menu_types_id', 'like', '%'.$request->type.'%'],
                        ['is_delivery', '=', 1]
                    ])->get();
                } else {
                    $search = Menu::with('promos:id,in_percent,menus_id', 'menu_types:id,name')->
                    where([
                        ['name', 'like', '%'.$request->name.'%'],
                        ['menu_types_id', 'like', '%'.$request->type.'%']
                    ])->get();
                }
            }
        } else {
            $data = explode(',', base64_decode($request->hash));
            $latitude = $data[0];
            $longitude = $data[1];
            
            $whereArray = [
                ['address', 'like', '%'.$request->address.'%'],
            ];

            $selectRaw = "*,
            round(6371 * acos(cos(radians($latitude)) 
            * cos(radians(restaurants.latitude)) 
            * cos(radians(restaurants.longitude) 
            - radians($longitude)) 
            + sin(radians($latitude)) 
            * sin(radians(restaurants.latitude))), 0) AS distance, if(scheduled = 1, 'close',
            if(open_at < close_at, (case when '$request->now' < close_at  and '$request->now' >= open_at THEN 'open' else 'close' end),
            (case when '$request->now' > close_at  and '$request->now' >= open_at THEN 'open' else 'close' end))) as now";

            $search = Resto::selectRaw($selectRaw)->where($whereArray)->orderBy('distance', 'ASC')->get();
        }

        return response([
            'search' => $search,
            'message' => 'Success',
            'status_code' => http_response_code()
        ]);
    }
}
