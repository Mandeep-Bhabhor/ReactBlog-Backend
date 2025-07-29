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

    public function index(Request $request)
    {
                $blog = Blog::orderByDesc('created_at');

         if(!empty($request->keyword)){
             $blog = $blog->where('title','like','%'. $request->keyword .'%');
         }

      $blog = $blog->paginate(4);

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




            $blog->image = $imageName;
            $blog->save();


            $sorcepath = public_path('uploads/temp/' . $tempImg->image);
            $destpath = public_path('uploads/blogs/' . $imageName);



            File::copy($sorcepath, $destpath);
        }
        return response()->json([
            'status' => true,
            'message' => 'blog added ',
            'data' => $blog,
        ]);
    }

    public function update($id, Request $request)
    {


        $blog = Blog::find($id);

        if ($blog == null) {
            return response()->json([
                'status' => false,
                'message' => 'blog not found',

            ]);
        }
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





        $blog->title = $request->title;
        $blog->shortDesc = $request->shortDesc;
        $blog->description = $request->description;
        $blog->author = $request->author;
        $blog->save();

        $tempImg = TempImg::find($request->image_id);

        if ($tempImg != null) {


            File::delete(public_path('uploads/blogs/' . $blog->image));
            $imgarray = explode('.', $tempImg->image);
            $ext = last($imgarray);
            $imageName = time() . '-' . $blog->id . '.' . $ext;




            $blog->image = $imageName;
            $blog->save();


            $sorcepath = public_path('uploads/temp/' . $tempImg->image);
            $destpath = public_path('uploads/blogs/' . $imageName);

            if (!File::exists($sorcepath)) {
                return response()->json(['error' => 'Source file does not exist', 'path' => $sorcepath], 404);
            }

            File::copy($sorcepath, $destpath);
        }
        return response()->json([
            'status' => true,
            'message' => 'blog updated ',
            'data' => $blog,
        ]);
    }

    public function delete($id)
    {

        $blog = Blog::find($id);

        if ($blog == null) {
            return response()->json([
                'status' => false,
                'message' => 'blog not found',

            ]);
        }
        //    delete from images
        File::delete(public_path('uploads/blogs/' . $blog->image));

        // delete from database

        $blog->delete();
        return response()->json([
            'status' => true,
            'message' => 'blog deleted ',

        ]);
    }
}
