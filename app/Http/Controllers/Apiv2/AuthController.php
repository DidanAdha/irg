<?php 
namespace App\Http\Controllers\Apiv2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use App\User;

use Hash;
use Storage;
use DB;

class AuthController extends Controller{

	function login(Request $request){
		if ($request->has('email') and $request->has('password')) {
			if ($this->loginAction($request->email, $request->password)) {
				$accessToken = Auth::user()->createToken('authToken')->accessToken;
				if($request->has('device_id')){
					$user = User::find(Auth::id());
					$user->device_id = $request->device_id;
					$user->save();
				}
				return response([
				    'user' => Auth::user(),
				    'access_token' => $accessToken,        
				    'status_code' => http_response_code()
				]);
			}else{
				return response([
	                'message' => 'Your email or password is not valid',
	                'status_code' => 422
	            ]);
			}
		}else{
			return response([
                'message' => 'Invalid input',
                'status_code' => 400
            ]);
		}
	}

	function loginGoogle(Request $request){
		if (!User::where('email', $request->email)->exists()) {
			$newUser = new User;
			$newUser->name = $request->name;
			$newUser->email = $request->email;
			$newUser->email_verified_at = date('Y-m-d H:i:s');
			$newUser->ttl = date_format(date_create($request->ttl), 'Y-m-d');
			$newUser->gender = $request->gender;
			if ($request->has('phone')) {
				$newUser->phone_number = $request->phone;
			}
			$newUser->save();
			if ($request->has('photoUrl')) {
                $fileContents = file_get_contents($request->photoUrl);
                File::put(public_path() . '/storage/user/' . $newUser->id . ".jpg", $fileContents);
                $newUser->img = '/storage/user/'.$newUser->id . ".jpg";
            }
            $newUser->save();
		}
		if ($this->loginAction($request->email)) {
			if($request->has('device_id')){
				$user = User::find(Auth::id());
				$user->device_id = $request->device_id;
				$user->save();
			}
			$accessToken = Auth::user()->createToken('authToken')->accessToken;
			return response([
			    'user' => Auth::user(),
			    'access_token' => $accessToken,        
			    'status_code' => http_response_code()
			]);
		}else {
			return response([
                'message' => 'Your email or password is not valid',
                'status_code' => 422
            ]);
		}
	}

	function editProfile(Request $request){
		$img = false;
		$user = User::find(auth()->id());
		$user->name = $request->name;
		$user->email = $request->email;
		$user->ttl = date_format(date_create($request->ttl), 'Y-m-d');
		$user->gender = $request->gender;
		$user->phone_number = $request->phone;
		if ($request->has('password')) $user->password = Hash::make($request->password);
		if ($request->has('photo') and $request->photo != '') {
			$data = $request->photo;
			list($type, $data) = explode(';', $data);
			list(, $type) = explode('/', $type);
			list(, $data) = explode(',', $data);
			$data = base64_decode($data);
			$name_img = '/'.'storage/user/img'.Auth::user()->id.date('Ymdhis').'.'.$type;
            $location = '/public'.'/user/img'.Auth::user()->id.date('Ymdhis').'.'.$type;
            if(Storage::put($location, $data)){
                $user->img = $name_img;
                $img = true;
                // ImageOptimizer::optimize(public_path($name_img));
            }
		}
		$user->save();
		return response([
		    'user' => $user,        
		    'status_code' => http_response_code()
		]);
	}

	function logout(Request $request){
		DB::table('oauth_access_tokens')
        ->where('user_id', Auth::user()->id)
        ->update([
            'revoked' => true
        ]);
        return response([      
		    'status_code' => http_response_code()
		]);
	}

	private function loginAction($email, $password = null) {
		if ($password == null) {
			return Auth::loginUsingId(User::where('email', $email)->first()->id);
		}else{
			return Auth::attempt(['email' => $email, 'password' => $password]);
		}
	}
}


?>