<?php 
namespace App\Http\Controllers\Apiv2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use App\MenuType as Type;
use App\Facility;
use App\Cuisine; 


class UtilController extends Controller {
	public function getData(Request $request){
		if ($request->query('q') == 'menutype') $data = Type::select('id','name')->get();
		elseif($request->query('q') == 'cuisine') $data = Cuisine::select('id','name')->get();
		elseif($request->query('q') == 'facility') $data = Facility::select('id','name')->get();
		else{
			$type = Type::select('id','name')->get();
			$cui = Cuisine::select('id','name')->get();
			$data = $type->merge($cui);
		}
		return response([
			'data' => $data,
			'status_code' => http_response_code()
		]);
	}

}


?>