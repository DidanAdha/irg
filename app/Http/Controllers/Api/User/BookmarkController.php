<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Bookmark;
use App\Menu;
use App\Restaurant as Resto;
use Auth;

class BookmarkController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function index(Request $request) {
        if ($request->tab == 1) {
            $menu = Menu::with('promos:id,in_percent,menus_id', 'menu_types:id,name')->whereHas('bookmarks', function($query){
                return $query->where('users_id', Auth::user()->id);
            })->get();
        } else {
            $menu = Menu::with('promos:id,in_percent,menus_id', 'menu_types:id,name')->whereHas('bookmarks', function($query){
                return $query->where('users_id', Auth::user()->id);
            })->where('is_delivery', 1)->get();
        }

        foreach ($menu as $p) {
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
            'menu' => $menu,
            'message' => 'Success',
            'status_code' => http_response_code()
        ]);
    }

    public function bookmark(Request $request) {
        $bookmarked = Bookmark::where('menus_id', $request->menus_id)->where('users_id', Auth::user()->id)->first();

        if (isset($bookmarked)) {
            Bookmark::find($bookmarked->id)->delete();
        } else {
            $bookmark = new Bookmark;
            $bookmark->users_id = Auth::user()->id;
            $bookmark->menus_id = $request->menus_id;
            $bookmark->save();
        }
        
        return response([
            'message' => 'Success',
            'status_code' => http_response_code()
        ]);
    }

    public function unbookmark() {

    }
}
