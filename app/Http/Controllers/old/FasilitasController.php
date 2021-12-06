<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Facility;
class FasilitasController extends Controller
{
    public function all(REQUEST $request){
        $search = $request->search;
        $get = Facility::where('name','like',"%".$search.'%')->paginate(15);
        return view ('master.fasilitas' , compact('get'));
    }
    public function insert(REQUEST $request){
        $insert = new Facility();
        $insert->name = $request->name;
        $insert->save();
        return redirect()->back();
    }
    public function edit($id){
        $get = Facility::find($id); 
        return view('master.fasilitasEdit', compact('get'));
    }
    public function update(REQUEST $request, $id){
        $get = Facility::find($id);
        $get->name = $request->name;
        $get->save();
        return redirect()->back();
    }
    public function destroy($id){
        $get = Facility::find($id);
        $get->delete();
        return redirect()->back(); 
    }
    
}
