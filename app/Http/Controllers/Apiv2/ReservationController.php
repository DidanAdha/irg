<?php 
namespace App\Http\Controllers\Apiv2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;

use App\Reservation;
use App\Restaurant as Resto;

class ReservationController extends Controller{
	public function store(Request $request){
		$res = new Reservation;
		$res->users_id = Auth::id();
		$res->restaurants_id = $request->resto;
		$res->chair = $request->people;
		$res->price = $request->price;
		$res->status = 'pending';
		$res->schedule = date_format(date_create($request->time), 'Y-m-d H:i:s');
		$res->save();
		return response([      
		    'status_code' => http_response_code()
		]);
	}
}

?>