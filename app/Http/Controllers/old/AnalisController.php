<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Restaurant;
use App\Menu;
use Carbon\carbon;
use App\Transaction;
use App\TransactionDetail;
class AnalisController extends Controller
{
    public function all (REQUEST $request){
        $option = $request->by;
        $search = $request->search;

        if ($option == null) {
            $get = Restaurant::where('name','like',"%".$search.'%')->orderBy('created_at', 'DESC')->paginate(15);
            
            return view ('resto' , compact('get'));
    
        }else{
            $get = Restaurant::where($option,'like',"%".$search.'%')->orderBy('created_at', 'DESC')->paginate(15);
            return view ('resto' , compact('get'));
        }
    }
    public function test(){
        $id = 1;
        $nowy = Carbon::now()->format('Y');
        $months = Transaction::selectRaw("MONTH(created_at) as month")->orderBy('created_at')->distinct()->get();
        $month = [];
        foreach($months as $mc){
            $month [] = $mc->month;
        }
        $sum = Transaction::select('users_id', DB::raw('sum(total_qty) as total'))->where('users_id','=', $id)->whereYear('created_at',$nowy)->groupBy('users_id')->groupBy(DB::raw("month(created_at)"))->orderBy('created_at')->get();
        $total = Transaction::select( DB::raw('sum(total_qty) as total'))->where('users_id','=', $id);
        $ma = [];
        $data = [];
        foreach($sum as $mp){
            $ma [] = $mp->menu_id;
            $data [] = (int)$mp->total;
        }
        return view('chart', compact('ma', 'data','month', 'sum', 'total'));
        
    }
    public function sort(REQUEST $request , $id){
        $date = $request->date;
        if($date == null){
            $tahun = Carbon::now()->format('Y');
            $months = Transaction::selectRaw("MONTH(created_at) as month")->orderBy('created_at')->distinct()->get();
            $month = [];
            foreach($months as $mc){
                $month [] = $mc->month;
            }
            $sum = Transaction::select('restaurants_id', DB::raw('sum(total_qty) as total'))->where('restaurants_id','=', $id)->where('is_done','=',1)->whereYear('created_at',$tahun)->groupBy('restaurants_id')->groupBy(DB::raw("month(created_at)"))->orderBy('created_at')->get();
            $total = Transaction::select( DB::raw('sum(total_qty) as total'))->where('restaurants_id','=', $id);
            $ma = [];
            $data = [];
            foreach($sum as $mp){
                $ma [] = $mp->menu_id;
                $data [] = (int)$mp->total;
            }
            return view('RestoInfoAll', compact('ma', 'data','month', 'sum', 'total', 'tahun'));
            // $sum = TransactionDetail::select('menu_id', DB::raw('sum(qty) as total'))->groupBy('menu_id')->get();
        // $total = TransactionDetail::select( DB::raw('sum(qty) as total'))->where('transaction_id','=',$id)->first();
        // return view('RestoInfoAll',compact('sum','total'));    
    }
        else{
            $filter = $request->by;
            $name = explode('-',$date);
            $hari = $name[0];
            $bulan = $name[1];
            $tahun = $name[2];
                if($filter == 1){
                    //current
                    // $sum = TransactionDetail::select('menu_id', DB::raw('sum(qty) as total'))->where('transaction_id','=',$id)->whereYear('created_at','=',$tahun)->whereMonth('created_at','=',$bulan)->whereDay('created_at','=',$hari)->groupBy('menu_id')->get();
                    // $total = TransactionDetail::select( DB::raw('sum(qty) as total'))->where('transaction_id','=',$id)->first();
                    // return view('RestoInfoAll',compact('sum','total'));
                    $months = Transaction::selectRaw("hour(created_at) as hour")->whereYear('created_at','=',$tahun)->whereMonth('created_at','=',$bulan)->whereDay('created_at','=',$hari)->orderBy('created_at')->distinct()->get();
                    $month = [];
                    foreach($months as $mc){
                        $month [] = $mc->hour;
                    }
                    $sum = Transaction::select('restaurants_id', DB::raw('sum(total_qty) as total'))->where('restaurants_id','=', $id)->where('is_done','=',1)->whereMonth('created_at','=',$bulan)->whereYear('created_at',$tahun)->whereDay('created_at',$hari)->groupBy('restaurants_id')->groupBy(DB::raw("hour(created_at)"))->orderBy('created_at')->get();
                    $total = Transaction::select( DB::raw('sum(total_qty) as total'))->where('restaurants_id','=', $id);
                    $ma = [];
                    $data = [];
                    foreach($sum as $mp){
                        $ma [] = $mp->menu_id;
                        $data [] = (int)$mp->total;
                    }
                    return view('RestoInfoAll', compact('ma', 'data','month', 'sum', 'total', 'tahun'));
                }                
                else if($filter == 2){
                    //done
                    $months = Transaction::selectRaw("day(created_at) as day")->whereYear('created_at','=',$tahun)->whereMonth('created_at','=',$bulan)->orderBy('created_at')->distinct()->get();
                    $month = [];
                    foreach($months as $mc){
                        $month [] = $mc->day;
                    }
                    
                    $sum = Transaction::select('restaurants_id', DB::raw('sum(total_qty) as total'))->where('restaurants_id','=', $id)->where('is_done','=',1)->whereMonth('created_at','=',$bulan)->whereYear('created_at',$tahun)->groupBy('restaurants_id')->groupBy(DB::raw("day(created_at)"))->orderBy('created_at')->get();
                    $total = Transaction::select( DB::raw('sum(total_qty) as total'))->where('restaurants_id','=', $id);
                    $ma = [];
                    $data = [];
                    foreach($sum as $mp){
                        $ma [] = $mp->menu_id;
                        $data [] = (int)$mp->total;
                    }
                    return view('RestoInfoAll', compact('ma', 'data','month', 'sum', 'total', 'tahun'));
                    // $sum = TransactionDetail::select('menu_id', DB::raw('sum(qty) as total'))->where('transaction_id','=',$id)->whereYear('created_at','=',$tahun)->whereMonth('created_at','=',$bulan)->groupBy('menu_id')->get();
                    // $total = TransactionDetail::select( DB::raw('sum(qty) as total'))->where('transaction_id','=',$id)->first();
                    // return view('RestoInfoAll',compact('sum','total'));
                }
                else if($filter == 3){
                //done 
                    $months = Transaction::selectRaw("MONTH(created_at) as month")->orderBy('created_at')->distinct()->get();
                    $month = [];
                    foreach($months as $mc){
                        $month [] = $mc->month;
                    }
                    $sum = Transaction::select('restaurants_id', DB::raw('sum(total_qty) as total'))->where('restaurants_id','=', $id)->where('is_done','=',1)->whereYear('created_at',$tahun)->groupBy('restaurants_id')->groupBy(DB::raw("month(created_at)"))->orderBy('created_at')->get();
                    $total = Transaction::select( DB::raw('sum(total_qty) as total'))->where('restaurants_id','=', $id);
                    $ma = [];
                    $data = [];
                    foreach($sum as $mp){
                        $ma [] = $mp->menu_id;
                        $data [] = (int)$mp->total;
                    }
                    return view('RestoInfoAll', compact('ma', 'data','month', 'sum', 'total', 'tahun'));
                    // $sum = TransactionDetail::select('menu_id', DB::raw('sum(qty) as total'))->where('transaction_id','=',$id)->whereYear('created_at','=',$tahun)->groupBy('menu_id')->get();
                    // $total = TransactionDetail::select( DB::raw('sum(qty) as total'))->where('transaction_id','=',$id)->first();
                    // return view('RestoInfoAll',compact('sum'));
                }
        }
    }
}