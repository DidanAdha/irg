<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use  Mail;
use App\Feedback;
use App\User;
class MailController extends Controller
{
    public function sendMail(Request $request, $id){

            $user = User::find($id);
            $nama = $request->name;
            $email = $request->email;
            $isi = $request->konten;
            $subject = 'Testing2';
            
            $name = Auth::user()->name;
//             // -----------------------------------
            Mail::send('template1', ['nama' => $nama,'name' => $name, 'isi' => $isi], function ($message) use ($email, $subject)
            {
                $message->subject($subject);
                $message->from('faizallaravel@gmail.com', 'test');
                $message->to($email);
                // dd($konten);
            });

            $hp = $request->id;
            $Get = Feedback::find($hp);
            $Get->message = $isi;
            $Get->save();  
            
            return back()->with('alert-success','Berhasil Kirim Email');
        
        
            
        }
    

}
