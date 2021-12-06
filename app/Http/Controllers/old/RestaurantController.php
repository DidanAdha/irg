<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Restaurant;
class RestaurantController extends Controller
{
    public function non(REQUEST $request, $id){
        $get = Restaurant::find($id);
        $get->status = 0;
        $get->save();
       return redirect()->back(); 
    }
    public function act(REQUEST $request, $id){
        $get = Restaurant::find($id);
        $get->status = 'active';
        $get->save();
       return redirect()->back(); 
    }
    public function destroy(REQUEST $request, $id){
        $get = Restaurant::find($id);
        $get->delete();
       return redirect()->back(); 
    }
}
