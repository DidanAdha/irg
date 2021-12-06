<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MenuType;
class MenuTypeController extends Controller
{
    public function all(REQUEST $request){
        $search = $request->search;
        $get = MenuType::where('name','like',"%".$search.'%')->paginate(15);
        return view ('master.Tmenu',compact('get'));
    }
    public function insert(REQUEST $request){
        $insert = new MenuType();
        $insert->name = $request->name;
        $insert->save();
        return redirect('menutype');
    }
    public function edit($id){
        $get = MenuType::find($id); 
        return view('master.TmenuEdit', compact('get'));
    }
    public function update(REQUEST $request, $id){
        $get = MenuType::find($id);
        $get->name = $request->name;
        $get->save();
        return redirect()->back();
    }
    public function destroy($id){
        $get = MenuType::find($id);
        $get->delete();
       return redirect()->back(); 
    }
}
