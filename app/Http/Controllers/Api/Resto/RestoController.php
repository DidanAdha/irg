<?php

namespace App\Http\Controllers\Api\Resto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Auth;
use File;
use QrCode;
use ImageOptimizer;
use App\Restaurant as Resto;
use App\RestaurantTable as Table;
use App\User;
use App\RestaurantFacility as Facility;
use App\RestaurantCuisine as Cuisine;
use App\ReservationPrice;
use App\RestoImage;
use App\Follow;


class RestoController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function generateRandomString($length = 152) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function index() {
        if (Auth::user()->roles_id == 4) {
            $resto = Resto::where('users_id', Auth::user()->id)->get();
            return response([
                'resto' => $resto,
                'message' => 'Success',
                'status_code' => http_response_code()
            ]);
        } else {
            return response([
                'message' => 'Not found',
                'status_code' => http_response_code()
            ], 404);
        }
        
    }

    public function detail($id) {
        $resto = Resto::with('cuisines:cuisines.id,name', 'facilities:facilities.id,name', 'reservation_prices', 'images')->where('id', $id)->first();
        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $resto->id)->first();
        } else {
            $id = $resto->id;
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        }

        if (isset($check)) {
            $f = count(Follow::where('restaurants_id', $id)->get());
            if ($f > 1000000) $follow = strval($f/1000000).'M';
            elseif($f > 1000) $follow = strval($f/1000).'K';
            else $follow = $f;
            return response([
                'resto' => $resto,
                'follow' => $follow,
                'message' => 'Success',
                'status_code' => http_response_code()
            ]);
        } else {
            return response([
                'message' => 'Not Found',
                'status_code' => 404
            ], 404);
        }
    }
    public function addImage(Request $request){
        $data = $request->img_encode;
        list($type, $data) = explode(';', $data);
        list(, $type) = explode('/', $type);
        list(, $data) = explode(',', $data);
        $data = base64_decode($data);
        
        if (isset($data)) {
            $name_img = '/'.'storage/resto_img/img'.Auth::user()->id.date('Ymdhis').'.'.$type;
            $location = '/'.'resto_img/img'.Auth::user()->id.date('Ymdhis').'.'.$type;
            if(Storage::disk('public')->put($location, $data)){
                $image = new RestoImage;
                $image->restaurant_id = $request->restaurant;
                $image->img = $name_img;
                $image->save();
                ImageOptimizer::optimize(public_path($name_img));
                return response([
                    'message' => 'Success',
                    'status_code' => http_response_code()
                ]);
            }
        }
    }
    public function deleteImage($id){
        $resto = RestoImage::find($id);
        if (File::exists(public_path($resto->img))) {
            if ($resto->img != '/storage/resto_img/default.jpg') {
                File::delete(public_path($resto->img));
            }

            $resto->delete();
            return response([
                'message' => 'Success',
                'status_code' => http_response_code()
            ]);
        }
    }
    public function add(Request $request) {
        
        // return count($facility);
        $name_img = "/storage/resto_img/default.jpg";

        $validated = $this->validate($request, [
            'name' => 'required',
            'address' => 'required',
            'phone_number' => 'required',
            'desc' => 'required',
            'start_price' => 'required|numeric|min:0',
            'end_price' => 'required|numeric',
        ]);

        $data = $request->img_encode;
        list($type, $data) = explode(';', $data);
        list(, $type) = explode('/', $type);
        list(, $data) = explode(',', $data);
        $data = base64_decode($data);
        
        if (isset($data)) {
            $name_img = '/'.'storage/resto_img/img'.Auth::user()->id.date('Ymdhis').'.'.$type;
            $location = '/'.'resto_img/img'.Auth::user()->id.date('Ymdhis').'.'.$type;
            Storage::disk('public')->put($location, $data);
            ImageOptimizer::optimize(public_path($name_img));
        }

        $resto = new Resto;
        $resto->users_id = Auth::user()->id;
        $resto->name = $validated['name'];
        $resto->desc = $validated['desc'];
        $resto->latitude = $request->latitude;
        $resto->longitude = $request->longitude;
        $resto->address = $validated['address'];
        $resto->phone_number = $validated['phone_number'];
        $resto->start_price = $request->start_price;
        $resto->end_price = $request->end_price;
        $resto->cities_id = $request->cities_id;
        $resto->open_at = $request->open_at;
        $resto->close_at = $request->close_at == '00:00:00' ? '23:59:59' : $request->close_at;
        $resto->img = $name_img;
        $resto->ongkir = $request->ongkir;
        // $resto->ongkir2 = $request->ongkir2;
        // $resto->ongkir3 = $request->ongkir3;
        $resto->can_reservation = $request->reservation;
        $resto->can_delivery = $request->delivery;
        $resto->can_take_away = $request->take_away;
        $resto->status = 'nonactive';
        $resto->save();

        if ($resto->can_reservation == 1) {
            $price = new ReservationPrice;
            $price->restaurants_id = $resto->id;
            $price->chair4 = $request->chair4;
            $price->chair8 = $request->chair4*2; 
            $price->chair12 = $request->chair4*3;
            $price->chair16 = $request->chair4*4;
            $price->etc = $request->chair4*5;
            $price->save();
        }

        for ($i=1;$i<=$request->table;$i++) {
            $table = new Table;
            $table->name = $i;
            $table->restaurants_id = $resto->id;
            $table->save();

            $newTable = Table::find($table->id);

            $barcode = "".$resto->id.""."".$table->id."".$this->generateRandomString();
            $img = QrCode::format('png')->merge('/public/irg.png')->size(300)->generate($barcode);
            $location = '/barcode'.'/'.$resto->id.'/meja'.$i.'.png';
            Storage::disk('public')->put($location, $img);

            $newTable->barcode = $barcode;
            $newTable->img = '/storage'.$location;
            $newTable->save();
        }

        $facility = explode(',', $request->facility);
        $resto->facilities()->attach($facility);

        $cuisine = explode(',', $request->cuisine);
        $resto->cuisines()->attach($cuisine);

        return response([
            'message' => 'Success',
            'status_code' => http_response_code()
        ]);
    }

    public function edit(Request $request, $id) {
        $resto = Resto::find($id);

        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $resto->id)->first();
        } else if (Auth::user()->roles_id == 5) {
            $id = $resto->id;
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        } else {
            $check = null;
        }

        if (isset($check)) {
            $name_img = $resto->img;

            $validated = $this->validate($request, [
                'name' => 'required',
                'address' => 'required',
                'phone_number' => 'required',
                'desc' => 'required',
                'start_price' => 'required|numeric|min:0',
                'end_price' => 'required|numeric',
            ]);
            
            $data = $request->img_encode;

            if ($data != 'null') {
                list($type, $data) = explode(';', $data);
                list(, $type) = explode('/', $type);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);

                if (isset($data)) {
                    $name_img = '/'.'storage/resto_img/img'.Auth::user()->id.date('Ymdhis').'.'.$type;
                    $location = '/'.'resto_img/img'.Auth::user()->id.date('Ymdhis').'.'.$type;
                    Storage::disk('public')->put($location, $data);
                    if (File::exists(public_path($resto->img))) {
                        if ($resto->img != '/storage/resto_img/default.jpg') {
                            File::delete(public_path($resto->img));
                        }
                    }
                    ImageOptimizer::optimize(public_path($name_img));
                }
            }
            
            $resto->name = $validated['name'];
            $resto->desc = $validated['desc'];
            $resto->latitude = $request->latitude;
            $resto->longitude = $request->longitude;
            $resto->address = $validated['address'];
            $resto->phone_number = $validated['phone_number'];
            $resto->start_price = $request->start_price;
            $resto->end_price = $request->end_price;
            $resto->cities_id = $request->cities_id;
            $resto->open_at = $request->open_at;
            $resto->close_at = $request->close_at == '00:00:00' ? '23:59:59' : $request->close_at;;
            $resto->img = $name_img;
            $resto->ongkir = $request->ongkir;
            // $resto->ongkir2 = $request->ongkir2;
            // $resto->ongkir3 = $request->ongkir3;
            $resto->can_reservation = $request->reservation;
            $resto->can_delivery = $request->delivery;
            $resto->can_take_away = $request->take_away;
            $resto->save();

            if ($resto->can_reservation == 1) {
                if (isset($resto->reservation_prices)) {
                    $price = ReservationPrice::find($resto->reservation_prices->id);
                    $price->restaurants_id = $id;
                    $price->chair4 = $request->chair4;
                    $price->chair8 = $request->chair4*2; 
                    $price->chair12 = $request->chair4*3;
                    $price->chair16 = $request->chair4*4;
                    $price->etc = $request->chair4*5;
                    $price->save();
                } else {
                    $price = new ReservationPrice;
                    $price->restaurants_id = $id;
                    $price->chair4 = $request->chair4;
                    $price->chair8 = $request->chair4*2; 
                    $price->chair12 = $request->chair4*3;
                    $price->chair16 = $request->chair4*4;
                    $price->etc = $request->chair4*5;
                    $price->save();
                }
            } else {
                if (isset($resto->reservation_prices)) {
                    $resto->reservation_prices->delete();
                }
            }

            $facility = explode(',', $request->facility);
            $resto->facilities()->sync($facility);

            $cuisine = explode(',', $request->cuisine);
            $resto->cuisines()->sync($cuisine);

            return response([
                'resto' => $resto,
                'message' => 'Success',
                'status_code' => http_response_code()
            ]);
        } else {
            return response([
                'message' => 'Not Found',
                'status_code' => 404
            ], 404);
        }
    }

    public function delete($id) {
        $resto = Resto::find($id);
        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $resto->id)->first();
        } else if (Auth::user()->roles_id == 5) {
            $id = $resto->id;
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        } else {
            $check = null;
        }

        if (isset($check)) {
            if (File::exists(public_path($resto->img))) {
                if ($resto->img != '/storage/resto_img/default.jpg') {
                    File::delete(public_path($resto->img));
                }
            }
            
            $resto->facilities()->detach();
            $resto->cuisines()->detach();
            $resto->delete();
    
            return response([
                'message' => 'Success',
                'status_code' => http_response_code()
            ]);
        } else {
            return response([
                'message' => 'Not Found',
                'status_code' => 404
            ], 404);
        }
    }

    public function addLogo(Request $request, $id) {
        $resto = Resto::find($id);

        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $resto->id)->first();
        } else if (Auth::user()->roles_id == 5) {
            $id = $resto->id;
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        } else {
            $check = null;
        }

        if (isset($check)) {
            $data = $request->img_encode;

            if ($data != 'null') {
                list($type, $data) = explode(';', $data);
                list(, $type) = explode('/', $type);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);

                if (isset($data)) {
                    $name_logo = '/'.'storage/resto_logo/img'.$resto->id.date('Ymdhis').'.'.$type;
                    $location = '/'.'resto_logo/img'.$resto->id.date('Ymdhis').'.'.$type;
                    Storage::disk('public')->put($location, $data);
                    if (File::exists(public_path($resto->logo))) {
                        if ($resto->logo != '/storage/resto_logo/default.png') {
                            File::delete(public_path($resto->logo));
                        }
                    }

                    $resto->logo = $name_logo;
                    $resto->save();

                    return response([
                        'message' => 'Success',
                        'status_code' => http_response_code()
                    ]);
                } else {
                    return response([
                        'message' => 'Image Not Found',
                        'status_code' => http_response_code()
                    ]);
                }
            } else {
                return response([
                    'message' => 'Image Not Found',
                    'status_code' => http_response_code()
                ]);
            }
        } else {
            return response([
                'message' => 'Not Found',
                'status_code' => 404
            ], 404);
        }
    }
}
