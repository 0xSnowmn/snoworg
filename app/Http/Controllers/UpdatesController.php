<?php

namespace App\Http\Controllers;

use App\programs;
use App\Updates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class UpdatesController extends Controller {

    public function __construct() {
        $this->middleware('auth:api');
    }

    public function all(){
        $all = DB::table('updates')->join('programs','updates.program','=','programs.program')->
        select('updates.*', 'programs.program')->get(); 
        return response()->json([
            "status" => true,
            "message" => "All Updates",
            "data" => $all
        ]);
    }

    public function create(Request $request){
        $request->validate( [
            'program' => 'required|string|exists:programs,program',
            'version' => 'required',
        ]);
        $update = programs::where('program','=',$request->program)->first();
        $update->version = $request->version;
        $update->save();
        return Updates::Create($request->all());
    }

    public function update(Request $request,$id){
        $update = Updates::find($id);
        if($update == null){
            return response()->json([
            "status" => false,
            "message" => "Update Not Found",
        ]);
        }
        $request->validate([
            'program' => 'required',
            'version' => 'required|string',
        ]);
        foreach($request->all() as $key => $value){
            $update->$key = $value;
        }
        $update->save();
        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => $update
        ]);
    }

    public function delete ($id){
        $update = Updates::find($id);
        if($update == null){
            return response()->json([
            "status" => false,
            "message" => "Update Not Found",
        ]);
        }
        $update->delete();
        return $update;
    }
    
}
