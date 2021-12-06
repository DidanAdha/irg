<?php

namespace App\Http\Controllers\Api\Resto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use QrCode;
use Auth;
use File;
use ZipArchive;
use App\User;
use App\RestaurantTable as Table;

class TableController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function generateRandomString($length = 148) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function index(Request $request) {
        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $request->resto)->first();
        } else {
            $id = $request->resto;
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        }

        if (isset($check)) {
            $table = Table::select('id', 'name', 'status')->where('restaurants_id', $request->resto)->get();
            return response([
                'table' => $table,
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

    public function add(Request $request) {
        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $request->restaurants_id)->first();
        } else if (Auth::user()->roles_id == 5) {
            $id = $request->restaurants_id;
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        } else {
            $check = null;
        }

        if (isset($check)) {
            $validated = $this->validate($request, [
                'table' => 'numeric|required|min:1'
            ]);
    
            $name = 0;
            $get_table = Table::select('id', 'name')->where('restaurants_id', $request->restaurants_id)->orderBy('name', 'DESC')->first();

            if (isset($get_table)) {
                $name = $get_table->name;
            }
    
            for ($i=1;$i<=$validated['table'];$i++) {
                $table = new Table;
                $table->name = $name+=1;
                $table->restaurants_id = $request->restaurants_id;
                $table->save();
    
                $newTable = Table::find($table->id);
    
                $barcode = "".$table->restaurants_id.""."".$table->id."".$this->generateRandomString();
                $img = QrCode::format('png')->merge('/public/irg.png')->size(300)->generate($barcode);
                $location = '/barcode'.'/'.$table->restaurants_id.'/meja'.$name.'.png';
                Storage::disk('public')->put($location, $img);
    
                $newTable->barcode = $barcode;
                $newTable->img = '/storage'.$location;
                $newTable->save();
            }
    
            return response([
                'table' => $table,
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

    public function edit(Request $request, $id) {
        $table = Table::find($id);

        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $table->restaurants_id)->first();
        } else if (Auth::user()->roles_id == 5) {
            $id = $table->restaurants_id;
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        } else {
            $check = null;
        }
        // return $check;
        if (isset($check)) {
            $table->status = $request->status;
            $table->save();

            return response([
                'message' => 'Update successful',
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
        $table = Table::find($id);

        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $table->restaurants_id)->first();
        } else if (Auth::user()->roles_id == 5) {
            $id = $table->restaurants_id;
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        } else {
            $check = null;
        }

        if (isset($check)) {
            if (File::exists(public_path($table->img))) {
                File::delete(public_path($table->img));
            }
            
            $table->delete();
    
            return response([
                'message' => 'Delete Successful',
                'status_code' => http_response_code()
            ]);
        } else {
            return response([
                'message' => 'Not Found',
                'status_code' => 404
            ], 404);
        }
    }

    public function deleteAll($id) {
        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $id)->first();
        } else if (Auth::user()->roles_id == 5) {
            // $id = $table->restaurants_id;
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        } else {
            $check = null;
        }

        if (isset($check)) {
            $table = Table::where('restaurants_id', $id)->get();
            File::deleteDirectory(public_path('storage/barcode/'.$table[0]->restaurants_id));

            foreach ($table as $i) {
                Table::find($i->id)->delete();
            }

            return response([
                'message' => 'Delete Successful',
                'status_code' => http_response_code()
            ]);
        } else {
            return response([
                'message' => 'Not Found',
                'status_code' => 404
            ], 404);
        }
    }

    public function downloadBarcode($id) {
        if (Auth::user()->roles_id == 4) {
            $check = Auth::user()->restaurants->where('id', $id)->first();
        } else if (Auth::user()->roles_id == 5) {
            // $id = $table->restaurants_id;
            $check = User::select('id', 'employees_id')->whereHas('employees', function($query) use($id){
                return $query->where('restaurants_id', $id);
            })->find(Auth::user()->id);
        } else {
            $check = null;
        }

        if (isset($check)) {
            $zip = new ZipArchive;
            $filename = '/storage/barcode_zip/barcode_resto_'.$id.'.zip';
            
            if ($zip->open(public_path($filename), ZipArchive::CREATE) === TRUE) {
                $files = File::files(public_path('storage/barcode/'.$id));

                foreach($files as $i) {
                    $relativeName = basename($i);
                    $zip->addFile($i, $relativeName);
                }

                $zip->close();
            }

            return response([
                'link' => $filename,
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
}
