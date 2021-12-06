<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Report;
use App\User;
use App\Restaurant;
class ReportController extends Controller
{
    public function all(REQUEST $request){
        $search = $request->search;
        $get = Report::where('restaurants_id','like',"%".$search.'%')->select('restaurants_id','users_id', DB::raw('count(*) as total'))->groupBy('restaurants_id')->get();
        return view('report.report', compact('get'));
    }
    public function cek($id){
        $resto = Restaurant::where('id','=',$id)->get();
        $get = Report::where('restaurants_id', '=' ,$id)->paginate(15);
        return view('report.reportcek', compact('get','resto'));
    }
}
