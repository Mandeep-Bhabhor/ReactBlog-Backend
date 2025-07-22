<?php

namespace App\Http\Controllers;

use App\Models\TempImg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TempimgController extends Controller
{
    public function store(Request $req)
    {

        $validate = Validator::make($req->all(), [
            'image' => 'required|image'
        ]);


        if ($validate->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'please upload jkkkkkkk',
                'errors' => $validate->errors()
            ]);
        }


        ///////file naming 
        $img = $req->image;
        $extension = $img->getClientOriginalExtension();
        $imgName = time() . '.' . $extension;

        //////Databse storing 
        $tempimg = new TempImg();
        $tempimg->image = $imgName;
        $tempimg->save();

        ////storing locally 
        $img->move(public_path('uploads/temp'), $imgName);


          return response()->json([
                'status' => true,
                'message' => 'image added ',
                'img' => $tempimg,
            ]);
    }
}
