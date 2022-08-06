<?php

namespace App\Http\Controllers;

use App\UrlShorter;
use Illuminate\Http\Request;

class UrlShorterController extends Controller {

    public function __construct() {
        $this->middleware('auth:api', ['except' => ['short']]);
    }

    public function short($short){
        $url = UrlShorter::where('short','=',$short)->first();
        if($url == null){
            return response()->json([
                "status" => false,
                "message" => "not found",
            ],404);
        }
        $url->increment('visits');
        return redirect()->away($url->url);

    }
    public function all(){
        return response()->json([
            "status" => true,
            "message" => "All Urls",
            "data" => UrlShorter::all()
        ]);
    }

    public function create(Request $request){
        $request->validate( [
            'name' => 'required|string',
            'url'=> 'required|url'
        ]);
        $shortUrl = new UrlShorter();
        $shortUrl->name = $request->name;
        $shortUrl->url = $request->url;
        $shortUrl->short = str_random(8);
        $shortUrl->save();
        return response()->json([
            "status" => true,
            "message" => "Created Success",
            "short url" => $shortUrl->short
        ]);
    }

    public function update(Request $request,$short){
        $shorter = UrlShorter::where('short','=',$short)->first();
        if($shorter == null){
            return response()->json([
            "status" => false,
            "message" => "Shorter Not Found",
        ]);
        }
        $request->validate( [
            'name' => 'required|string',
            'url'=> 'required|url'
        ]);
        foreach($request->all() as $key => $value){
            $shorter->$key = $value;
        }
        $shorter->save();
        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => $shorter
        ]);
    }

    public function delete ($short){
        $shorter = UrlShorter::where('short','=',$short)->first();;
        if($shorter == null){
            return response()->json([
            "status" => false,
            "message" => "Shorter Not Found",
        ]);
        }
        $shorter->delete();
        return $shorter;
    }
    
}
