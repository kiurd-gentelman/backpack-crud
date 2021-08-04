<?php

//use App\Http\Controllers\Admin\SubCategory;
use App\Http\Controllers\Admin\SubCategoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function (){
    return redirect(url('/admin'));
});

Route::get('image/delete/{id}', [SubCategoryController::class , 'image_delete'])->name('image-delete');
Route::get('api/article', [\App\Http\Controllers\Admin\SubCategoryController::class,'index']);
Route::get('api/article/{id}', [\App\Http\Controllers\Admin\SubCategoryController::class,'show']);
