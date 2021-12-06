<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Role;
class RoleController extends Controller
{
    public function all(REQUEST $request){
        $search = $request->search;
        $get = Role::where('name','like',"%".$search.'%')->paginate(15);
        return view ('master.role',compact('get'));
    }
    public function insert(REQUEST $request){
        $insert = new RoleType();
        $insert->name = $request->name;
        $insert->save();
        return redirect()->back();
    }
    public function edit($id){
        $get = Role::find($id); 
        return view('master.roleEdit', compact('get'));
    }
    public function update(REQUEST $request, $id){
        $get = Role::find($id);
        $get->name = $request->name;
        $get->save();
        return redirect()->back();
    }
    public function destroy($id){
        $get = Role::find($id);
        $get->delete();
       return redirect()->back(); 
    }
}
