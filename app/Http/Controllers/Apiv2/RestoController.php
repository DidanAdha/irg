<?php 
namespace App\Http\Controllers\Apiv2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use Storage;
use QrCode;

use App\Restaurant as Resto;
use App\Menu;
use App\RestoImage;
use App\MenuType;
use App\Promo;
use App\Follow;
use App\Cuisine;
use App\RestaurantCuisine as Rcuisine;
use App\Facility;
use App\RestaurantFacility as Rfasil;
use App\Transaction as Trans;
use App\RestaurantTable as Table;

class RestoController extends Controller{
	private function generateRandomString($length = 148) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

	public function detail($id){
		//prep data
		$minp = 0; $maxp = 0;
		$follow = Follow::where('restaurants_id', $id)->where('users_id', auth()->id());
		$resto = Resto::find($id);
		$image = RestoImage::select('img')->where('restaurant_id', $id)->get();
		$menu = Menu::where('restaurants_id', $id)->where('is_recommended', 0)->orderBy('menu_types_id')->get();
		$rmenu = Menu::where('restaurants_id', $id)->where('is_recommended', 1)->get();
		if (Menu::where('restaurants_id', $id)->exists()) {
			$maxp = Menu::where('restaurants_id', $id)->orderBy('price', 'desc')->first()->price;
			$minp = Menu::where('restaurants_id', $id)->orderBy('price', 'asc')->first()->price;
		}
		// $promo = Promo::with(['menus' => function($q) use ($resto){
		// 	$q->where('restaurants_id', $resto->id);
		// }])->where('is_active', 1)->limit(3)->latest()->get();
		$promo = Menu::where('restaurants_id', $id)->whereHas('promos', function($q){
			$q->where('is_active', 1);
		})->limit(3)->latest()->get();
		//prep return
		if ($follow->exists()) $data['is_followed'] = true;
		else  $data['is_followed'] = false;
		$data['id'] = $resto->id;
		$data['name'] = $resto->name;
		$data['address'] = $resto->address;
		$data['desc'] = $resto->desc;
		$data['ongkir'] = intval($resto->ongkir);
		$data['lat'] = $resto->latitude;
		$data['long'] = $resto->longitude;
		$data['openclose'] = date_format(date_create($resto->open_at), 'H:i').' - '.date_format(date_create($resto->close_at), 'H:i');
		$data['reservation_fee'] = $resto->reservation_fee == null ? 0 : intval($resto->reservation_fee);
		$main_img = RestoImage::where('restaurant_id', $id)->where('is_main', 1);
		$data['main_img'] = $main_img->exists() ? $main_img->first()->img : null;
		$data['img'] = [];
		foreach ($image as $i) $data['img'][] = $i->img;
		foreach ($rmenu as $rm) {
			$rm->delivery_price = intval($rm->delivery_price);
			$rm->price = intval($rm->price);
		}
		$data['recom'] = $rmenu;
		$data['range'] = $minp == $maxp ? "IDR ".intval($minp / 1000).'K' : "IDR ".intval($minp / 1000).'K - '.intval($maxp / 1000).'K';
		//operate first data
		$types = [];
		foreach ($menu as $m) {
			$kategori = MenuType::find($m->menu_types_id)->name;
			
			$mpromo = Promo::where('menus_id', $m->id)->get();
			if ($mpromo->isNotEmpty()) {
				if ($mpromo[0]->discount != null) {
					$m->discounted = $m->price - ($m->price * $mpromo[0]->discount / 100);
				}elseif($mpromo[0]->potongan != null){
					$m->discounted = $m->price - $mpromo[0]->potongan;
				}
			}
			$bSearch = array_search($kategori, $types);
			if (!$bSearch) {
				$types[] = $kategori;
				$bSearch = array_search($kategori, $types);
				$data['menu'][$bSearch]['name'] = $kategori;
			}
			$m->delivery_price = intval($m->delivery_price);
			$m->price = intval($m->price);
			$data['menu'][$bSearch]['menu'][] = $m;
		}
		// return json_encode($resto);
		// foreach ($promo as $p) {
		// 	if ($p->discount != null) {
		// 		$word = 'Diskon '.$p->discount.'% untuk pembelian '.$p->menus->name;
		// 		$discounted_price = $p->menus->price - ($p->menus->price * $p->discount / 100);
		// 	}elseif($p->potongan != null){
		// 		$word = 'Potongan '.($p->potongan / 1000).' ribu untuk pembelian '.$p->menus->name;
		// 		$discounted_price = $p->menus->price - $p->potongan;
		// 	}elseif($p->ongkir != null){
		// 		$word = 'Diskon ongkir'.($p->ongkir / 1000).' ribu untuk pembelian '.$p->eanus->name;
		// 	}
		// 	$data['promo'][] = [
		// 		'word' => $word,
		// 		'menu_id' => $p->menus->id,
		// 		'menu_name' => $p->menus->name,
		// 		'menu_desc' => $p->menus->desc,
		// 		'menu_img' => $p->menus->img,
		// 		'menu_price' => intval($p->menus->price),
		// 		'menu_delivery_price' => $p->menus->delivery_price, 
		// 		'menu_discounted' => $discounted_price ? $discounted_price : 0
		// 	];
		// }
		foreach ($promo as $p) {
			if ($p->promos->discount != null) {
				$word = 'Diskon '.$p->promos->discount.'% untuk pembelian '.$p->name;
				$discounted_price = $p->price - ($p->price * $p->promos->discount / 100);
			}elseif($p->promos->potongan != null){
				$word = 'Potongan '.($p->promos->potongan / 1000).' ribu untuk pembelian '.$p->name;
				$discounted_price = $p->price - $p->promos->potongan;
			}elseif($p->promos->ongkir != null){
				$word = 'Diskon ongkir'.($p->promos->ongkir / 1000).' ribu untuk pembelian '.$p->name;
			}
			$data['promo'][] = [
				'word' => $word,
				'menu_id' => $p->id,
				'menu_name' => $p->name,
				'menu_desc' => $p->desc,
				'menu_img' => $p->img,
				'menu_price' => intval($p->price),
				'menu_delivery_price' => $p->delivery_price, 
				'menu_discounted' => $discounted_price ? $discounted_price : 0
			];
		}
		if(empty($promo)) $data['promo'] = [];
		//return
		return response([
            'data' => $data,
            'status_code' => http_response_code()
        ]);
	}

	public function makefav(Request $request){
		$follow = Follow::where('restaurants_id', $request->id)->where('users_id', auth()->id());
		if ($follow->exists()) {
			$follow->first()->delete();
			$action = 'deleted';
		}else{
			$follow = new Follow;
			$follow->users_id = auth()->id();
			$follow->restaurants_id = $request->id;
			$follow->save();
			$action = 'created';
		}
		return response([
			'action' => $action,
			'status_code' => http_response_code()
		]);
	}

	public function edit(Request $request){
		// $resto = Resto::find($request->id);
		// foreach ($request->field as $i => $field) {
		// 	if ($field == 'name') {
		// 		# code...
		// 	}elseif($field == 'desc'){

		// 	}elseif($field == 'address'){

		// 	}
		// }
	}

	public function createMenu(Request $request,$id = null){
		if ($request->has('img')) {
			$data = $request->img;
			list($type, $data) = explode(';', $data);
			list(, $type) = explode('/', $type);
			list(, $data) = explode(',', $data);
			$data = base64_decode($data);
			$name_img = '/'.'storage/menu_img/img'.Auth::user()->id.date('Ymdhis').'.'.$type;
	        $location = '/public'.'/menu_img/img'.Auth::user()->id.date('Ymdhis').'.'.$type;
	        Storage::put($location, $data);
		}
        if ($id == null) {
        	$menu = new Menu;
        }else{
        	$menu = Menu::find($id);
        }
		$menu->restaurants_id = $request->resto;
		$menu->name = $request->name;
		$menu->desc = $request->desc;
		$menu->price = $request->price;
		$menu->delivery_price = $request->delivery_price;
		$menu->is_recommended = $request->is_recommended == 'true' ? 1 : 0;
		if (isset($name_img)) $menu->img = $name_img;
		$menu->menu_types_id = $request->type;
		$menu->save();
		return response([
			'status_code' => http_response_code()
		]);
	}

	public function createImage(Request $request){
		$data = $request->img;
		list($type, $data) = explode(';', $data);
		list(, $type) = explode('/', $type);
		list(, $data) = explode(',', $data);
		$data = base64_decode($data);
		$name_img = '/'.'storage/resto_img/img'.Auth::user()->id.date('Ymdhis').'.'.$type;
        $location = '/public'.'/resto_img/img'.Auth::user()->id.date('Ymdhis').'.'.$type;
        if(Storage::put($location, $data)){
            $image = new RestoImage;
                $image->restaurant_id = $request->resto;
                $image->img = $name_img;
                $image->save();
        }
		return response([
			'status_code' => http_response_code()
		]);
	}

	public function getDetailResto(Request $request){
		$resto = Resto::where('users_id', Auth::id())->first();
		if ($resto == null) {
			return response([
				'msg' => 'User tidak punya resto',
				'status_code' => http_response_code()
			]);
		}else{
			$trx = Trans::where('restaurants_id', $resto->id)->where('status', '!=', 'done')->count();
			return response([
				'notif' => $trx,
				'resto' => $resto,
				'status_code' => http_response_code()
			]);
		}
	}

	public function store(Request $request){
		//save data
		$resto = new Resto;
		$resto->users_id = Auth::id();
		$resto->name = $request->name;
		$resto->desc = $request->desc;
		$resto->latitude = $request->latitude;
		$resto->longitude = $request->longitude;
		$resto->address = $request->address;
		$resto->phone_number = $request->phone;
		$resto->open_at = explode(" - ", $request->hours)[0];
		$resto->close_at = explode(" - ", $request->hours)[1];
		$resto->can_delivery = $request->has('ongkir') ? 1 : 0;
		$resto->ongkir = $request->has('ongkir') ? $request->ongkir : 0;
		$resto->can_reservation = $request->has('re_price') ? 1 : 0;
		$resto->reservation_fee = $request->has('re_price') ? $request->re_price : 0;
		$resto->can_take_away = $request->takeaway;
		// $resto->id = 14;
		$resto->save();
		//save img
		$data = $request->img;
		list($type, $data) = explode(';', $data);
		list(, $type) = explode('/', $type);
		list(, $data) = explode(',', $data);
		$data = base64_decode($data);
		$name_img = '/'.'storage/resto_img/img'.Auth::user()->id.date('Ymdhis').'.'.$type;
        $location = '/public'.'/resto_img/img'.Auth::user()->id.date('Ymdhis').'.'.$type;
        if(Storage::put($location, $data)){
            $image = new RestoImage;
            $image->restaurant_id = $resto->id;
            $image->img = $name_img;
            $image->save();
        }
        //store side data
        $tipeResto = explode(', ', $request->type);
        $fasils = explode(', ', $request->fasilitas);
        foreach ($tipeResto as $tipe) {
        	$dbTipe = Cuisine::where('name', $tipe)->first();
        	// return json_encode(strtolower($tipe));
        	$rc = new Rcuisine;
        	$rc->cuisine_id = $dbTipe->id;
        	$rc->restaurant_id = $resto->id;
        	$rc->save();
        }
        foreach ($fasils as $fasil) {
        	$dbF = Facility::where('name', $fasil)->first();
        	$rf = new Rfasil;
        	$rf->facility_id = $dbF->id;
        	$rf->restaurant_id = $resto->id;
        	$rf->save();
        }
        // $this->generateQr($request->table);
        return response([
			'status_code' => http_response_code()
		]);
	}

	public function generateQr($amount){
		$resto = Resto::where('users_id', Auth::id())->first();
		for($i = 0; $i < $amount; $i++){
			$table = new Table;
			$barcode = "".$table->restaurants_id.""."".$table->id."".$this->generateRandomString();
	  		$img = QrCode::format('png')->merge('/public/irg.png')->size(300)->generate($barcode);
	  		$location = '/barcode'.'/'.$table->restaurants_id.'/meja-'.$i.'.png';
	  		Storage::disk('public')->put($location, $img);

	  		$table->name = $i;
            $table->restaurants_id = $resto->id;
            $table->barcode = $barcode;
            $table->img = '/storage'.$location;
            $table->save();
		}
	}

}

?>