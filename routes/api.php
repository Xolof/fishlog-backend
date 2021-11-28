<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\FishCatchController;
use App\Http\Controllers\PublicController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [ApiController::class, 'authenticate']);
Route::post('register', [ApiController::class, 'register']);
Route::get('public_fishcatch', [PublicController::class, 'index']);

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('logout', [ApiController::class, 'logout']);
    Route::get('get_user', [ApiController::class, 'get_user']);
    Route::get('fishcatch', [FishCatchController::class, 'index']);
    Route::get('fishcatch/{id}', [FishCatchController::class, 'show']);
    Route::post('create', [FishCatchController::class, 'store']);
    Route::put('update/{id}',  [FishCatchController::class, 'update']);
    Route::delete('delete/{id}',  [FishCatchController::class, 'destroy']);
});