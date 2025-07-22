<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\TempimgController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');




Route::get('showblog',[BlogController::class,'index']);
Route::get('blog/{id}',[BlogController::class,'show']);


Route::post('createblog',[BlogController::class,'store']);

Route::post('save-img',[TempimgController::class,'store']);