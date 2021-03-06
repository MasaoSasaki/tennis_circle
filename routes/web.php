<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\AlbumController as AdminAlbumController;
use App\Http\Controllers\Admin\HomeController as AdminHomeController;
use App\Http\Controllers\Admin\ImageController as AdminImageController;
use App\Http\Controllers\Admin\AlbumUserController as AdminAlbumUserController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
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

Route::get('/', function () {
  return view('welcome');
})->name('root');
Route::get('/feedback', [HomeController::class, 'feedback']);
Route::get('/privacy-policy', [HomeController::class, 'privacy_policy']);
Route::get('/terms', [HomeController::class, 'terms']);

Route::group(['middleware' => 'auth'], function() {
  Route::resource('albums', AlbumController::class)->only(['index', 'show']);
});

Route::group(['middleware' => 'basicauth', 'prefix' => 'admin'], function() {
  Route::get('/', [AdminHomeController::class, 'index']);
  Route::resource('/albums', AdminAlbumController::class)->except(['show']);
  Route::resource('/images', AdminImageController::class)->only(['index', 'store']);
  Route::post('/images/create', [AdminImageController::class, 'createImage']);
  Route::post('/images/{id}', [AdminImageController::class, 'destroyImage']);
  Route::delete('/album_user', [AdminAlbumUserController::class, 'destroy']);
  Route::get('/users', [AdminUserController::class, 'index']);
  Route::post('/private_user/{id}', [AdminUserController::class, 'privateUser']);
  Route::post('/public_user/{id}', [AdminUserController::class, 'publicUser']);
});

Auth::routes();

