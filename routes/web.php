<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Auth;
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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/send_message',[MessageController::class,'send']);
Route::get('/recieve_message',[MessageController::class,'recieve']);
Route::get('/private_chat',[HomeController::class,'privateChat'])->name('private');
Route::get('/users',[HomeController::class,'users'])->name('users');
Route::get('/fetch_private_message/{user}',[MessageController::class,'fetchPrivateMessage']);
Route::post('/send_private_message/{user}',[MessageController::class,'sendPrivateMessage']);