<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Carbon\carbon;
use \App\User;
use App\Role;
use App\Restaurant;
use App\Feedback;
use App\Cuisine;
use Auth;

class UserController extends Controller
{
    public function role(){

        $lili = Role::all()->where('type','=','admin');
        return view('master.userinput', compact('lili'));
    
    }
    //////////
    public function edit(REQUEST $request, $id){
        $get = User::find($id); 
        $role = $get->roles_id;
        $namer = Role::find($role);
        $lili = Role::all();
       
        return view('master.useredit', compact('get','namer','lili'));
    }
    public function update(REQUEST $request, $id){
        $get = User::find($id);
        $get->name = $request->name;
        $get->email = $request->email;
        $get->address = $request->address;
        $get->ttl = $request->ttl;
        $get->roles_id = $request->roles_id;
        $get->phone_number = $request->phone_number;
        $get->password =  Hash::make($request->password);
        $get->save();
        return redirect()->back();
    }
    public function non(REQUEST $request, $id){
        $get = User::find($id);
        $get->active = 0;
        $get->save();
       return redirect()->back(); 
    }
    public function act(REQUEST $request, $id){
        $get = User::find($id);
        $get->active = 1;
        $get->save();
       return redirect()->back(); 
    }
    public function destroy(REQUEST $request, $id){
        $get = User::find($id);
        $get->delete();
       return redirect()->back(); 
    }
    //////////////////////
    public function insert(Request $request){
        $add = new User();
        $add->name = $request->name;
        $add->email = $request->email;
        $add->address = $request->address;
        $add->ttl = $request->ttl;
        $add->roles_id = $request->roles_id;
        $add->phone_number = $request->phone_number;
        $add->password =  Hash::make($request->password);
        $add->save();
        return redirect()->back();
    }
    public function admin(REQUEST $request){
        $option = $request->by;
        $search = $request->search;
        $typer = Role::where('type','=','admin')->get();
        $datuk = [];
        foreach ($typer as $typex){
            $datuk[] = $typex->id;
        }
        if ($option == null) {
            $get = User::WhereIn('roles_id', $datuk)->where('name','like',"%".$search.'%')->paginate(15);
            $list = Role::where('type','=','admin')->get();
            return view ('users.useradmin' , compact('get','list'));
    
        }else{
        $list = Role::where('type','=','admin')->get();
        $get = User::Where('roles_id', $option)->where('name','like',"%".$search.'%')->paginate(15);
                $list = Role::where('type','=','admin')->get();
                return view ('users.useradmin' , compact('get','list'));
        }
    }
    public function biasa(REQUEST $request){
        $option = $request->by;
        $search = $request->search;
        $typer = Role::where('type','=','nonadmin')->get();
        $datuk = [];
        foreach ($typer as $typex){
            $datuk[] = $typex->id;
        }
        if ($option == null) {
            $get = User::WhereIn('roles_id', $datuk)->where('name','like',"%".$search.'%')->paginate(15);
            $list = Role::where('type','=','nonadmin')->get();
            return view ('users.userbiasa' , compact('get','list'));
    
        }else{
        $list = Role::where('type','=','admin')->get();
        $get = User::Where('roles_id', $option)->where('name','like',"%".$search.'%')->paginate(15);
                $list = Role::where('type','=','admin')->get();
                return view ('users.userbiasa' , compact('get','list'));
        }
    }
    public function status (REQUEST $request){
        $get = new User ();
        $status = 0;
        $get->status = $request->$status;
        $get->save();
        return redirect()->back();
    }
    public function filter(REQUEST $request, $id){
        $userid = $request->$id;
        $filt = $request->filter;
    }
    public function dashboard(REQUEST $request){
        if (Auth::user()->roles_id == 8) {
            echo "Authentication Success, please continue to the app";
            return;
        }
        $btn = $request->ih;
        $customer = User::where('roles_id','=', 6)->count();
        $restaurant = Restaurant::all()->count();
        $admin = User::WhereBetween('roles_id', [1,3])->count();
        /////////////////////////////
        $feedback = Feedback::limit(2)->orderBy('created_at', 'desc')->where('status','=','pending')->get();

        /////////////////////////////
        if($btn == null){
            $title="Customer";
            $btn1="secondary";
            $btn2="primary";
            $name="jumlah customer";
        /////////////////////////////////////////////////////
        $year = Carbon::now()->format('Y');
        $months = User::selectRaw("MONTH(created_at) as month, created_at")->whereYear('created_at', $year)->orderBy('created_at')->distinct()->get();
        $month = [];
        foreach($months as $mc){
            $month [] = $mc->month;
        }
        $sum = User::select(DB::raw('count(*) as total'))->where('roles_id','=',6)->whereYear('created_at',$year)->groupBy(DB::raw("month(created_at)"))->orderBy('created_at')->get();
        $data = [];
        foreach($sum as $mp){
            $data[] = $mp->total ;
        }
        /////////////////////////////////////////////////////
        return view('dashboard', compact('customer','restaurant','data', 'year', 'month','admin','title','btn1','btn2','name','feedback'));
        }else{
            $title="Restaurant";
            $btn1="primary";
            $btn2="secondary";
            $name="jumlah restaurant";
            /////////////////////////////////////////////////////
            $year = Carbon::now()->format('Y');
            $months = Restaurant::selectRaw("MONTH(created_at) as month")->whereYear('created_at', $year)->orderBy('created_at')->distinct()->get();
            $month = [];
            foreach($months as $mc){
                $month [] = $mc->month;
            }
            $sum = Restaurant::select('id', DB::raw('count(id) as total'))->whereYear('created_at',$year)->groupBy(DB::raw("month(created_at)"))->orderBy('created_at')->get();
            $data = [];
            foreach($sum as $mp){
                $data[] = $mp->total ;
            } 
            /////////////////////////////////////////////////////
        return view('dashboard', compact('customer','restaurant','data', 'year', 'month','admin','title','btn1','btn2','name','feedback'));
        }
    }
}