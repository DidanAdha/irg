<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RestaurantType;
use App\Cuisine;
class RestaurantTypeController extends Controller
{
    public function all(REQUEST $request){
        $search = $request->search;
        $get = Cuisine::where('name','like',"%".$search.'%')->paginate(15);
        return view ('master.Tresto',compact('get'));
    }
    public function insert(REQUEST $request){
        $insert = new Cuisine();
        $insert->name = $request->name;
        $insert->save();
        return redirect()->back();
    }
    public function edit($id){
        $get = Cuisine::find($id); 
        return view('master.TrestoEdit', compact('get'));
    }
    public function update(REQUEST $request, $id){
        $get = Cuisine::find($id);
        $get->name = $request->name;
        $get->save();
        return redirect()->back();
    }
    public function destroy($id){
        $get = Cuisine::find($id);
        $get->delete();
       return redirect()->back(); 
    }
}
