<?php

namespace App\Http\Controllers;

use App\Activates;
use App\programs;
use App\Updates;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Facades\DB;
date_default_timezone_set('Africa/Cairo');
class ActivatesController extends Controller {

    public function __construct() {
        //$this->middleware('auth:api', ['except' => ['activateRequest', 'request','login']]);
    }

    public function exportKey($mac,$program,$version){
        $mac = substr(md5($mac), 0, 10);
        $program = substr(md5($program), 0, 10);
        $version = substr(md5($version), 0, 10);
        $final = $mac . '-' . $program . '-' . $version;
        $fullKey = md5($final);
        $parts = str_split($fullKey, 8);
        $final = implode("-", $parts);
        return $final;
    }

    public function activateRequest(Request $request){
        $request->validate( [
            'program'           => 'required|string|exists:programs,program',
            'version'           => 'required|string',
            'mac'               => 'required|string'
        ]);
        $mac = substr(md5($request->mac), 0, 10);
        $program = substr(md5($request->program), 0, 10);
        $version = substr(md5($request->version), 0, 10);
        $final = $mac . '-' . $program . '-' . $version;
        $fullKey = md5($final);
        $parts = str_split($fullKey, 8);
        $final = implode("-", $parts);
        return response()->json([
            "status" => true,
            "message" => "activate request success",
            "data" => $final
        ]);
    }
    
    public function all(){
        $all = DB::table('activates')->where('status','=','confirmed')->join('programs','activates.program','=','programs.id')->
        select('activates.*', 'programs.program')->get(); 
        return response()->json([
            "status" => true,
            "message" => "All Activates",
            "data" => $all
        ]);
    }

    public function waiting(){
        $all = Activates::where(function ($query) {
            $query->where('status', '=', 'waiting')
                  ->orWhere('status', '=', 'hold');
        })->join('programs','activates.program','=','programs.id')->
        select('activates.*', 'programs.program')->get();
        return response()->json([
            "status" => true,
            "message" => "All Waiting List",
            "data" => $all
        ]);
    }
    public function confirm(Request $request){
        $request->validate( [
            'active_request' => 'required|string|exists:activates,active_request',
            'expire'         => 'required|string'
        ]);
        $pass = str_random(5);
        $activate_req = Activates::where('active_request', '=', $request->active_request)->join('programs','activates.program','=','programs.id')->
        select('activates.*', 'programs.program')->first();
        $activate_req->status = 'confirmed';
        $activate_req->expire = Carbon::today()->addMonths($request->expire)->format('m-d-Y');
        $activate_req->activate_at = date('Y/m/d h:i:s');
        $activate_req->user = str_random(5);
        $activate_req->pass = sha1($pass);
        $activate_req->used = 1;
        $activate_req->save();
        return response()->json([
            "status" => true,
            "message" => "Activate Request Confirmed Successfully",
            "data" => $activate_req,
            "pass" => $pass
        ]);
    }

    public function test(Request $request){
        $request->validate( [
            'active_request' => 'required|string|exists:activates,active_request',
        ]);

        $activate_req = Activates::where('active_request', '=', $request->active_request)->first();
        $activate_req->status = 'confirmed';
        $activate_req->expire = Carbon::tomorrow()->format('m-d-Y');
        $activate_req->activate_at = date('Y/m/d h:i:s');
        $activate_req->user = 'test';
        $activate_req->pass = sha1('test');
        $activate_req->used = 1;
        $activate_req->save();
        return response()->json([
            "status" => true,
            "message" => "Test Activate Created Successfully",
            "data" => $activate_req
        ]);
    }

    public function request(Request $request){
        $request->validate( [
            'active_request' => 'required|string|unique:activates,active_request',
            'version' => 'required',
            'program' => 'required|string|exists:programs,program',
            'mac' => 'required|string',
        ]);
        $program = programs::where('program','=',$request->program)->first();
        $activate = new Activates();
        $activate->program = $program->id;
        foreach($request->all() as $key => $value ){
            if($key == 'program'){
                continue;
            }
            $activate->$key = $value;
        }
        $activate->save();
        return response()->json([
            "status" => true,
            "message" => "All Activates",
            "data" => $activate
        ]);
    }

    private function checkExpire($expire,$program,$version){
        $from_date = Carbon::parse(date('m-d-Y', strtotime($expire))); 
        $through_date = Carbon::parse(date('m-d-Y', strtotime(Carbon::now()->format('m-d-Y'))));         
        $shift_difference = $from_date->greaterThanOrEqualTo($through_date);
        if($shift_difference){
            $up_version = $this->checkUpdate($program,$version);
            if($up_version !== true){
                return response()->json([
                    "status" => false,
                    "update" => true,
                    "version" => $up_version,
                ],401);
            };
            return response()->json([
                "status" => true,
                "expired" => false,
                "message" => "Logged In Successfully",
            ]);
           } else {
            return response()->json([
                "status" => false,
                "expired" => true,
                "message" => "Your Activation Is Expired. Contact The Owner To ReActivate It",
            ],401);
           }
    }

    public function login(Request $request){
        $request->validate( [
            'active_request' => 'required|string',
            'version' => 'required',
            'program' => 'required|string|exists:programs,program',
            'mac' => 'required|string',
            'user' => 'required|string|min:4',
            'pass' => 'required|string|min:4'
        ]);
        $user = Activates::where([['active_request','=',$request->active_request]])->first();
        if(!empty($user)){
            $reHashKey = $this->exportKey($request->mac,$request->program,$request->version);
            if($reHashKey == $request->active_request){
                if($request->user == $user->user && sha1($request->pass) == $user->pass){
                    if($user->status == 'waiting'){
                        return response()->json([
                            "status" => false,
                            "expired" => false,
                            "message" => "Your Request Didn't Confirmed Yet",
                        ],400);
                    } elseif($user->status == 'hold'){
                        return response()->json([
                            "status" => false,
                            "expired" => false,
                            "message" => "Your Activation Is Hold, Contact Owner",
                        ],402);
                    }
                   return $this->checkExpire($user->expire,$request->program,$request->version);
                } else {
                    return response()->json([
                        "status" => false,
                        "expired" => false,
                        "message" => "Wrong User Or Password",
                    ],401);
                } 
            } else {
                return response()->json([
                    "status" => false,
                    "expired" => false,
                    "message" => "Error",
                ],401);
            }
        }
    }

    public function checkUpdate($program,$prog_version){
        $program = Updates::select('version')->where('program','=',$program)->groupBy('version')->get();
        $status = true;
        foreach($program as $prog){
            $version = (int) str_replace('v','',strtolower($prog->version)) ;
            $prog_version = (int) str_replace('v','',strtolower($prog_version));
            if($version > $prog_version){
                return $version;
            }
        }
        return true;
    }
    

    public function delete ($id){
        $activate = Activates::find($id);
        if($activate == null){
            return response()->json([
            "status" => false,
            "message" => "Activate Not Found",
        ],404);
        }
        $activate->delete();
        return $activate;
    }

    public function stop_key(Request $request){
        $request->validate( [
            'active_request' => 'required|string|exists:activates,active_request',
        ]);

        $key = Activates::where('active_request','=',$request->active_request)->first();
        if($key == null){
            return response()->json([
                "status" => false,
                "message" => "Activate Not Found",
            ],404);
        }
        if($key->status == 'hold'){
            $key->status = 'confirmed';
        } elseif($key->status == 'confirmed'){
            $key->status = 'hold';
        }
        $key->save();
        return response()->json([
            "status" => false,
            "message" => "Activate Key " . ucfirst($key->status) . " Success",
            "activate" => $key
        ]);
    }

    public function reactivate(Request $request){
        $request->validate( [
            'active_request' => 'required|string|exists:activates,active_request',
            'expire' => 'required|string',
        ]);

        $key = Activates::where('active_request','=',$request->active_request)->first();
        if($key == null){
            return response()->json([
                "status" => false,
                "message" => "Activate Not Found",
            ],404);
        }
        $key->expire = Carbon::createFromFormat('m-d-Y',$key->expire)->addMonths((int)$request->expire)->format('m-d-Y');
        $key->save();
        return response()->json([
            "status" => false,
            "message" => "Activate Key Reactivated Success",
        ]);
    }
    
}
