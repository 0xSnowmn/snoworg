<?php

namespace App\Http\Controllers;

use App\programs;
use App\Activates;
use Illuminate\Http\Request;
use Validator;

class ProgramsController extends Controller {
    //$all = Activates::where('program','=');
    public function all(){
        return response()->json([
            "status" => true,
            "message" => "All Programs",
            "data" => programs::all(),
            "keys" => Activates::all()
        ]);
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'program' => 'required|string|min:4|unique:programs,program',
            'price' => 'required|string',
            'url' => 'nullable|string',
            'version' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Inputs',
                'error' => $validator->errors()
            ], 422);
        }

        return programs::Create($request->all());
    }

    public function update(Request $request,$id){
        $program = programs::find($id);
        if($program == null){
            return response()->json([
            "status" => false,
            "message" => "Program Not Found",
        ]);
        }
        $request->validate([
            'program' => 'required',
            'price' => 'required|string',
            'url' => 'string',
            'version' => 'string',
        ]);
        foreach($request->all() as $key => $value){
            $program->$key = $value;
        }
        $program->save();
        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => $program
        ]);
    }

    public function delete ($id){
        $program = programs::find($id);
        if($program == null){
            return response()->json([
            "status" => false,
            "message" => "Program Not Found",
        ]);
        }
        $program->delete();
        return $program;
    }
    
}
