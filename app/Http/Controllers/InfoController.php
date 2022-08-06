<?php

namespace App\Http\Controllers;

use App\Info;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InfoController extends Controller {

    public function __construct() {
        $this->middleware('auth:api', ['except' => ['create']]);
    }

    public function all(){
        return response()->json([
            "status" => true,
            "message" => "All Information Of Users",
            "data" => Info::all()
        ]);
    }

    public function create(Request $request){
        $request->validate( [
            'mac_adress' => 'required|string|exists:activates,mac',
            'version'=> 'required|string',
            'program' => 'required|string|exists:programs,id',
            'os'=> 'required|string',
            'Pc_name' => 'required|string',
            'user_id'    => 'required|string|exists:activates,id'
        ]);
        $user = Info::where('user_id','=',$request->user_id)->first();
        if($user == null){
            $info = new Info(); 
            foreach($request->all() as $key => $value){
                $info->$key = $value;
            }
            $info->save();
            return response()->json([
                "status" => true,
                "message" => "Created Success",
                "info" => $info
            ]);
        }

        foreach($request->all() as $key => $value){
            $user->$key = $value;
        }
        $user->Last_opened = Carbon::now();
        $user->increment('count_using');
        $user->save();
        return response()->json([
            "status" => true,
            "message" => "Saved Success",
            "info" => $user
        ]);
    }

    public function update(Request $request,$id){
        $info = Info::where('user_id','=',$id)->first();
        if($info == null){
            return response()->json([
            "status" => false,
            "message" => "info Not Found",
        ]);
        }
        $request->validate( [
            'mac_adress' => 'required|string|exists:activates,mac',
            'version'=> 'required|string',
            'program' => 'required|string|exists:programs',
            'os'=> 'required|string',
            'Pc_name' => 'required|string',
        ]);
        foreach($request->all() as $key => $value){
            $info->$key = $value;
        }
        $info->save();
        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => $info
        ]);
    }

    public function delete ($id){
        $info = Info::where('user_id','=',$id)->first();
        if($info == null){
            return response()->json([
            "status" => false,
            "message" => "info Not Found",
        ]);
        }
        $info->delete();
        return $info;
    }
    
}
