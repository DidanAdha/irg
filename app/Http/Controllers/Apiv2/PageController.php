<?php 
namespace App\Http\Controllers\Apiv2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use DB;

use App\User;
use App\Menu;
use App\Restaurant as Resto;
use App\Transaction as Trans;
use App\Reservation;
use App\TransactionDetail as Detail;
use App\RestoImage;
use App\MenuType as Type;
use App\Cuisine;
use App\Promo;

class PageController extends Controller{
	private function count_distance($lat1, $lon1, $lat2, $lon2){
		if (($lat1 == $lat2) && ($lon1 == $lon2)) {
			return 0;
		}else{
			$theta = $lon1 - $lon2;
		    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
		    $dist = acos($dist);
		    $dist = rad2deg($dist);
		    $miles = $dist * 60 * 1.1515;
		    // $unit = strtoupper($unit);
		    return ($miles * 1.609344);
		}
	}

	private function nearby($lat, $long){
		return DB::raw("6371 * acos(cos(radians(" . $lat . ")) 
        * cos(radians(latitude)) 
		* cos(radians(longitude) - radians(" . $long . ")) 
        + sin(radians(" .$lat. ")) 
        * sin(radians(latitude))) AS distance");
	}

	public function home(Request $request){
		$data['banner'] = [];
		$banner = RestoImage::select('img')->limit(3)->get();
		foreach ($banner as $b) {
			$data['banner'][] = $b->img;
		}
		$data['promo'] = [];
		$loc = $this->nearby($request->query('lat'), $request->query('long'));
		$resto = Resto::select('id', 'name', $loc)
			->whereHas('images')
			->with('images')
			->orderBy('distance', 'asc')
			->limit(5)
			->get();
		$promo = Menu::whereHas('promos')->with(['restaurants' => function($q) use ($loc){
			$q->select('id','name','latitude','longitude',$loc)->orderBy('distance', 'asc');
		}])->limit(5)->get();
		$lagi = Resto::select('id', 'name', $loc)
			->whereHas('transactions', function($q){
				$q->where('users_id', auth()->id());
			})->whereHas('images')->limit(5)->latest()->get();
		$ongoing = Trans::select('id','restaurants_id','type', 'total','status', 'address', 'total', 'ongkir', 'created_at')->where('users_id', Auth::id())->where('status', '!=', 'done')->latest()->get();
		$ongoing_res = Reservation::where('status', '!=', 'finished')->where('status', '!=', 'declined')->where('users_id', Auth::id())->latest()->get();

		foreach ($resto as $r) {
			$r->distance = $r->distance < 1 ? 1.0 : round($r->distance, 2);
			$r->img = $r->images[0]->img;
			unset($r->images);
		}
		foreach ($lagi as $r) {
			$r->distance = $r->distance < 1 ? 1.0 : round($r->distance, 2);
			$r->img = $r->images[0]->img;
			unset($r->images);
		}
		foreach ($promo as $pr) {
			$p = $pr->promos;
			if ($p->discount != null) {
				$discounted_price = $pr->price - ($pr->price * $p->discount / 100);
			}elseif($p->potongan != null){
				$discounted_price = $pr->price - $p->potongan;
			}elseif($p->ongkir != null){
				continue;
			}
			$data['promo'][] = [
				'id' => $pr->id,
				'name' => $pr->name,
				'resto_id' => $pr->restaurants->id,
				'resto_name' => $pr->restaurants->name,
				'resto_distance' => $pr->restaurants->distance < 1 ? 1 : round($pr->restaurants->distance, 2),
				'img' => $pr->img,
				'price' => intval($pr->price),
				'discounted_price' => $discounted_price ? $discounted_price : 0
			];
		}
		$on = $ongoing_res->merge($ongoing);
		foreach ($on as $o) {
			$o->resto_name = $o->restaurants->name;
			$o->img = RestoImage::where('restaurant_id', $o->restaurants->id)->first()->img;
			if ($o->status == "pending") $o->status_text = "Menunggu";
			elseif($o->status == "process") $o->status_text = "Diproses";
			if (isset($o->schedule)) {
				$o->type_text = 'Reservasi untuk '.$o->chair.' orang';
				$o->date = date_format(date_create($o->schedule), 'j M Y, H:i');
				unset($o->schedule);
				$o->total = $o->price;
			}else{
				if($o->type == "delivery") $o->type_text = "Pesan antar";
				elseif($o->type == "takeaway") $o->type_text = "Ambil Langsung";
				elseif($o->type == "dinein") $o->type_text = "Makan Ditempat";
				$o->date = date_format(date_create($o->created_at), 'j M Y, H:i');
			}
			unset($o->restaurants);
			unset($o->created_at);
			$o->total = intval($o->total);
			// unset($o->status);
		}
		return response([
			'banner' => $data['banner'],
			'trans' => $on,
			'resto' => $resto,
			'promo' => $data['promo'],
			'again' => $lagi,
		]);
	}

	public function promo(Request $request){
		$data['promo'] = [];
		$loc = $this->nearby($request->query('lat'), $request->query('long'));
		if ($request->has('resto')) {
			$promo = Menu::whereHas('promos')->with([
				'restaurants' => function($q) use ($loc){
					$q->select('id','latitude','longitude',$loc)->orderBy('distance', 'asc');
				}
			])->where('restaurants_id', $request->query('resto'))->limit(20)->get();
		}else{
			$promo = Menu::whereHas('promos')->with([
				'restaurants' => function($q) use ($loc){
					$q->select('id','latitude','longitude',$loc);
				}
			])->with('restaurants')->limit(20)->get();
		}
		// dd($promo);
		foreach ($promo as $pr) {
			$p = $pr->promos;
			if ($p->discount != null) {
				$discounted_price = $pr->price - ($pr->price * $p->discount / 100);
			}elseif($p->potongan != null){
				$discounted_price = $pr->price - $p->potongan;
			}elseif($p->ongkir != null){
				continue;
			}
			$data['promo'][] = [
				'id' => $pr->id,
				'name' => $pr->name,
				'desc' => $pr->desc,
				'distance' => $this->count_distance($request->query('lat'), $request->query('long'),$pr->restaurants->latitude, $pr->restaurants->longitude),
				'img' => $pr->img,
				'price' => $pr->price,
				'discounted_price' => $discounted_price ? $discounted_price : 0
			];
		}

		return response([
			'promo' => $data['promo']
		]);
	}

	public function favResto(Request $request){
		$fav = Resto::select('id', 'name', $this->nearby(
				$request->query('lat'), 
				$request->query('long')))
			->whereHas('follows', function($q){
				$q->where('users_id', Auth::id());
			})->whereHas('images')->get();
		foreach ($fav as $r) {
			$r->distance = $r->distance < 1 ? 1 : round($r->distance, 2);
			$r->img = $r->images[0]->img;
			unset($r->images);
		}
		return response([
			'resto' => $fav
		]);
	}

	public function history(Request $request){
		$type = [
			'dinein' => 'Makan Ditempat',
			'takeaway' => 'Ambil Langsung',
			'delivery' => 'Pesan antar'
		];
		if ($request->has('id')) {
			$trans = Trans::find($request->query('id'));
			$data['resto'] = intval($trans->restaurants_id);
			$data['address'] = $trans->type == 'delivery' ? $trans->address : $trans->restaurants->address;
			$data['type'] = $type[$trans->type];
			$data['discount'] = intval($trans->discount);
			$data['ongkir'] = intval($trans->ongkir);
			$data['total'] = intval($trans->total + $trans->ongkir - $trans->discount);
			$data['menus'] = Detail::select('qty','menus_id','price')->where('transactions_id', $trans->id)->get();
			foreach ($data['menus'] as $menu) {
				$m = Menu::find($menu->menus_id);
				$menu->menus_id = intval($m->id);
				$menu->name = $m->name;
				$menu->desc = $m->desc;
				$menu->price = intval($m->price);
				$menu->img = $m->img;
			}
			return response($data);
		}else{
			$data['trans'] = [];
			$trans = Trans::where('users_id', auth()->id())
					->where('status', 'done')
					->get();

			foreach ($trans as $trx) {
				$d = [];
				$d['id'] = $trx->id;
				$d['resto'] = $trx->restaurants_id;
				$d['resto_img'] = RestoImage::where('restaurant_id', $d['resto'])->first();
				$d['resto_name'] = $trx->restaurants->name;
				$d['time'] = date_format(date_create($trx->created_at), 'j F Y, H:i');
				$d['price'] = $trx->total + $trx->ongkir - $trx->discount;
				$d['type'] = $type[$trx->type];
				if (isset($d['resto_img']->img)) {
					$d['resto_img'] = $d['resto_img']->img;
					$data['trans'][] = $d;
				}
				
			}
			return response($data);
		}
	}

	public function search(Request $request){
		$loc = $this->nearby($request->query('lat'), $request->query('long'));
		$q = $request->has('q') ? $request->query('q') : null;
		$isMenuOnly = Type::where('name', $request->query('type'))->exists();
		if (!$isMenuOnly) {
			$isCuisine = Cuisine::where('name', $request->query('type'))->exists();
			if (!$isCuisine and $q == null) return response([
				'status_code' => 404
			]);
		}
		//get data
		if ($isMenuOnly) {
			$type = Type::where('name', $request->query('type'))->first();
			$menus = Menu::where('menu_types_id', $type->id)->with(['restaurants' => function($q) use ($loc){
				$q->select('id','name','latitude','longitude',$loc)->orderBy('distance', 'asc');
			}])->get();
			foreach ($menus as $pr) {
				$data['menus'][] = [
					'id' => $pr->id,
					'name' => $pr->name,
					'resto_id' => $pr->restaurants->id,
					'resto_name' => $pr->restaurants->name,
					'resto_distance' => $pr->restaurants->distance < 1 ? 1 : round($pr->restaurants->distance, 2),
					'img' => $pr->img,
					'price' => intval($pr->price),
				];
			}	
		}elseif($isCuisine){
			$cui = Cuisine::where('name', $request->query('type'))->first();
			$restos = Resto::select('id', 'name', $loc)->whereHas('cuisines', function($qu) use ($cui){
				$qu->where('cuisine_id', $cui->id);
			})
			->whereHas('images')
			->with('images')
			->orderBy('distance', 'asc')
			->limit(5)
			->get();
			foreach ($restos as $r) {
				$r->distance = $r->distance < 1 ? 1 : round($r->distance, 2);
				$r->img = $r->images[0]->img;
				unset($r->images);
			}
		}else{
			$menus = Menu::where('name', 'LIKE', '%'.$q.'%')->get();

			foreach ($menus as $pr) {
				$p = Promo::where('menus_id', $pr->id);
				if ($p->exists()) {
					$p = $p->first();
					if ($p->discount != null) {
						$discounted_price = $pr->price - ($pr->price * $p->discount / 100);
					}elseif($p->potongan != null){
						$discounted_price = $pr->price - $p->potongan;
					}elseif($p->ongkir != null){
						continue;
					}
				}
				$data['menus'][] = [
					'id' => $pr->id,
					'name' => $pr->name,
					'resto_id' => $pr->restaurants->id,
					'resto_name' => $pr->restaurants->name,
					'resto_distance' => $pr->restaurants->distance < 1 ? 1 : round($pr->restaurants->distance, 2),
					'img' => $pr->img,
					'price' => intval($pr->price),
					'discounted_price' => isset($discounted_price) ? $discounted_price : 0
				];
			}
			$restos = Resto::select('id', 'name','address', $loc)
			->where(function($qu) use ($q){
				$qu->where('name', 'LIKE', '%'.$q.'%')->orWhere('address', 'LIKE', '%'.$q.'%');
			})
			->whereHas('images')
			->with('images')
			->orderBy('distance', 'asc')
			->limit(5)
			->get();
			foreach ($restos as $r) {
				$r->distance = $r->distance < 1 ? 1 : round($r->distance, 2);
				$r->img = $r->images[0]->img;
				unset($r->images);
			}
		}
		
		return response([
			'data' => $q,
			'menu' => isset($data['menus']) ? $data['menus'] : [],
			'resto' => isset($restos) ? $restos : [],
			'status_code' => http_response_code()
		]);
	}

	

}
?>