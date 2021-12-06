<?php 
namespace App\Http\Controllers\Apiv2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use App\User;

use App\Transaction as Trans;
use App\TransactionDetail as Detail;
use App\Menu;
use App\Chatroom;
use App\Restaurant as Resto;
use App\RestoImage;
use App\RestaurantTable as Table;
use App\Follow;

class TransactionController extends Controller {

	public function store(Request $request){
		// if (Trans::where('users_id', Auth::id())->where('status', 'pending')->count() > 2) {
		// 	return response([
		// 		'message' => 'Anda memiliki banyak transaksi yang belum selesai, silahkan selesaikan terlebih dahulu',
		// 		'status_code' => http_response_code()
		// 	]);
		// }
		$exploded_menu = explode(',', $request->menu);
		$exploded_qty = explode(',', $request->qty);
		if (empty($exploded_menu) or !$request->has('menu') or !$request->has('qty') or (count($exploded_menu) != count($exploded_qty))) {
			return response([
				'status_code' => 400
			]);
		}
		$resto = Resto::find(Menu::find($exploded_menu[0])->restaurants_id);
		if (!(date('H:i:s') > $resto->open_at and date('H:i:s') < $resto->close_at)) {
			return response([
				'message' => 'resto tutup',
				'status_code' => http_response_code()
			]);
		}
		$chatroom = '';
		$trx = new Trans;
		$trx->restaurants_id = $resto->id;
		$trx->users_id = Auth::id();
		$trx->discount = $request->discount;
		$trx->type = $request->type;
		
		if ($request->type == "delivery") {
			$trx->ongkir = $request->ongkir;
			$trx->address = $request->address;
			$trx->lat = $request->lat;
			$trx->long = $request->long;
		}
		if ($request->type == "dinein") {
			$table = Table::where('barcode', $request->barcode);
			if (!$table->exists()) {
				return response([
					'message' => 'bad request',
					'status_code' => http_response_code()
				]);
			}
			$table = $table->first();
			if ($table->status == 'empty') {
				$trx->restaurant_tables_id = $table->id;
				// $table->status = 'filled';
				$table->save();
			}else{
				return response([
					'message' => 'meja sudah diisi',
					'status_code' => http_response_code()
				]);
			}
		}
		$trx->status = 'pending';
		$trx->save();
		$total_price = 0;
		foreach ($exploded_menu as $i => $menu) {
			$detail = new Detail;
			$detail->transactions_id = $trx->id;
			$detail->menus_id = $menu;
			$detail->qty = $exploded_qty[$i];
			$detail->price = $request->type == "dinein" ? Menu::find($menu)->price : Menu::find($menu)->delivery_price;
			$detail->save();
			$total_price += $detail->price * $exploded_qty[$i];
		}
		if ($trx->type != 'dinein') {
		    $chatroom = new Chatroom;
		    $chatroom->users_id = Auth::id();
		    $chatroom->restaurants_id = $resto->id;
		    $chatroom->transactions_id = $trx->id;
		    $chatroom->save();
		}
		$trx->total = $total_price;
		$trx->save();
		$follow = Follow::where('restaurants_id', $resto->id)->where('users_id', auth()->id());
		if (!$follow->exists()) {
			$follow = new Follow;
			$follow->users_id = auth()->id();
			$follow->restaurants_id = $resto->id;
			$follow->save();
		}
		return response([
			'trx' => $trx,
			'chatroom' => $chatroom,
			'status_code' => http_response_code()
		]);
	}

	public function check(Request $request){
		$exploded = explode(',', $request->menu);
		if (empty($exploded) or !$request->has('menu')) {
			return response([
				'status_code' => 400
			]);
		}
		$resto = Resto::find(Menu::find($exploded[0])->restaurants_id);
		$menus = [];
		if (!(date('H:i:s') > $resto->open_at and date('H:i:s') < $resto->close_at)) {
			return response([
				'message' => 'resto tutup',
				'status_code' => http_response_code()
			]);
		}else{
			foreach ($exploded as $menu) {
				$menus[] = [
					'menu_id' => $menu,
					'ready' => true
				];
			// 	if (Menu::find($menu)->is_ready == 1) {
			// 		$menus[] = [
			// 			'menu_id' => $menu,
			// 			'ready' => true
			// 		];
			// 	}else{
			// 		$menus[] = [
			// 			'menu_id' => $menu,
			// 			'ready' => false
			// 		];
			// 	}
			}

			return response([
				'address' => $resto->address,
				'ongkir' => intval($resto->ongkir),
				'menu' => $menus,
				'status_code' => http_response_code()
			]);
		}
	}

	public function index($id){
		$detail = Detail::select('menus_id', 'qty', 'price')->where('transactions_id', $id)->whereHas('menus')->get();
		$trans = Trans::find($id);
		$data['trans']['type'] = $trans->type;
		$data['trans']['address'] = $trans->type == 'takeaway' ? Resto::find($trans->restaurants_id)->address : $trans->address;
		$data['trans']['ongkir'] = intval($trans->ongkir);
		$data['trans']['total'] = intval($trans->total);
		if ($trans->type == 'delivery' or $trans->type == 'takeaway') 
			$data['chatroom'] = Chatroom::where('transactions_id', $id)->first()->id;
		foreach ($detail as $d) {
			$d->name = $d->menus->name;
			$d->image = $d->menus->img;
			$d->desc = $d->menus->desc;
			$d->menus_id = intval($d->menus_id);
			$d->qty = intval($d->qty);
			$d->price = intval($d->price);
			unset($d->menus);
		}
		$data['menu'] = $detail;
		$data['status_code'] = http_response_code();
		return response($data);
	}

	public function restoHistory(){
		$type = [
			'dinein' => 'Makan Ditempat',
			'takeaway' => 'Ambil Langsung',
			'delivery' => 'Pesan antar'
		];
		if ($request->has('id')) {
			$trans = Trans::find($request->query('id'));
			$data['address'] = $trans->type == 'delivery' ? $trans->address : $trans->restaurants->address;
			$data['type'] = $type[$trans->type];
			$data['discount'] = $trans->discount;
			$data['ongkir'] = intval($trans->ongkir);
			$data['total'] = $trans->total + $trans->ongkir - $trans->discount;
			$data['menus'] = Detail::select('qty','menus_id','price')->where('transactions_id', $trans->id)->get();
			foreach ($data['menus'] as $menu) {
				$m = Menu::find($menu->menus_id);
				$menu->name = $m->name;
				$menu->desc = $m->desc;
				$menu->price = intval($menu->price);
			}
			return response($data);
		}else{
			$data['trans'] = [];
			$trans = Trans::whereHas('restaurants', function($q){
				$q->where('users_id', auth()->id());
			})->get();

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

	public function getTrans(){
		$types = [
			'delivery' => 'Pesan antar',
			'takeaway' => 'Ambil Sekarang',
			'dinein' => 'Makan Ditempat'
		];
		$ltrans = [];
		$resto = Resto::where('users_id', Auth::id())->first();
		$trans = Trans::where('restaurants_id', $resto->id)->where('status', '!=', 'done')->where('status', '!=', 'cancel')->get();
		foreach ($trans as $t) {
			$data['id'] = $t->id;
			$data['status'] = $t->status;
			$data['username'] = $t->users->name;
			if ($t->discount != 0) $data['before_discount'] = $t->total + $t->discount;
			$data['total'] = $t->total;
			$data['type'] = $types[$t->type];
			$ltrans[$t->status][] = $data;
		}
		return response([
			'trx' => $ltrans,
			'status_code' => http_response_code()
		]);
	}
	
	public function op($op, $id){
		$trx = Trans::find($id);
		if($op == 'acc') $trx->status = 'process';
		elseif($op == 'ready') $trx->status = 'ready';
		elseif($op == 'done') $trx->status = 'done';
		elseif($op == 'cancel') $trx->status = 'cancel';
		else return response([
			'msg' => 'bad request',
			'status_code' => 500,
		]);
		if(($op == 'done' or $op == 'cancel') and $trx->transaction_tables_id != null){
			$table = Table::find($trx->transaction_tables_id);
			$table->status = 'empty';
			$table->save();
		}
		$trx->save();
		return $this->getTrans();
	}
}
?>