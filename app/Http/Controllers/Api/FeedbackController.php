<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Feedback;
use Auth;

class FeedbackController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function index(Request $request) {
        $validated = $this->validate($request, [
            'desc' => 'required'
        ]);

        $feedback = new Feedback;
        $feedback->users_id = Auth::user()->id; 
        $feedback->desc = $validated['desc'];
        $feedback->save();

        return response([
            'feedback' => $feedback,
            'message' => 'Success',
            'status_code' => http_response_code()
        ]);
    }
}
