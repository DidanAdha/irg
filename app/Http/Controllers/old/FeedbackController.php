<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Feedback;
use App\User;
class FeedbackController extends Controller
{
    public function all (REQUEST $request){
        $option = $request->by;
        $search = $request->search;

        if ($option == null) {
            $get = Feedback::where('users_id','like',"%".$search.'%')->paginate(15);
            $list = Feedback::groupBy('status')->get();
            return view ('feedback' , compact('get','list'));
    
        }else{
            $get = Feedback::where('users_id','like',"%".$search.'%')->where('status','=',$option)->paginate(15);
            $list = Feedback::groupBy('status')->get();
            return view ('feedback' , compact('get','list'));
        }
        $get = Feedback::paginate(15);
        return view('feedback',compact('get'));
    }
    public function find ($id){
        $Get = Feedback::find($id);
        $name = User::find($Get->users_id);
        $Get->status = "read";
        $Get->save();  
        return view('feedback_user',compact('Get','name'));
    }
}
