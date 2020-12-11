<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Middleware\needNoConnexion;


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

Route::get('/socket', function () {
    return view('testSocket');
});




Route::get('/login', [LoginController::class, 'login'])->middleware(needNoConnexion::class);
Route::get('/signup', [LoginController::class, 'signup'])->middleware(needNoConnexion::class);
Route::get('/logout', [LoginController::class, 'logout']);
Route::post('/tryLogin', [LoginController::class, 'tryLogin'])->middleware(needNoConnexion::class);
Route::post('/trySignup', [LoginController::class, 'trySignup'])->middleware(needNoConnexion::class);
Route::get('/', [HomeController::class, 'index']);
