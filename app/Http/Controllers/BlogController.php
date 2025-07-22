<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\TempImg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class BlogController extends Controller
{
    //

    public function index()
    {

        $blog = Blog::orderByDesc('created_at')->get();

        return response()->json([
            'status' => 'true',
            'data' => $blog
        ]);
    }

    public function show($id)
    {
        $blog = Blog::find($id);

        if ($blog == null) {
            return response()->json([
                'status' => false,
                'message' => 'blog not found ',

            ]);
        } 

        $blog['date'] = \Carbon\Carbon::parse($blog->created_at)->format('d M Y');

          return response()->json([
                'status' => true,
                'data' => $blog
            ]);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'title' => 'required|min:5',
            'author' => 'required|min:3'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'please fffffix',
                'errors' => $validate->errors()
            ]);
        }




        $blog = new Blog();
        $blog->title = $request->title;
        $blog->shortDesc = $request->shortDesc;
        $blog->description = $request->description;
        $blog->author = $request->author;
        $blog->save();

        $tempImg = TempImg::find($request->image_id);

        if ($tempImg != null) {
            $imgarray = explode('.', $tempImg->image);
            $ext = last($imgarray);
            $imageName = time() . '-' . $blog->id . '.' . $ext;
        }



        $blog->image = $imageName;
        $blog->save();


        $sorcepath = public_path('uploads/temp/' . $tempImg->image);
        $destpath = public_path('uploads/blogs/' . $imageName);

        if (!File::exists($sorcepath)) {
            return response()->json(['error' => 'Source file does not exist', 'path' => $sorcepath], 404);
        }

        File::copy($sorcepath, $destpath);

        return response()->json([
            'status' => true,
            'message' => 'blog added ',
            'data' => $blog,
        ]);
    }

    public function update() {}

    public function delete() {}
}
