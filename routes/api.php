<?php

use App\Http\Controllers\ConfigController;
use App\Http\Controllers\GameController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(GameController::class)->group(function () {
    Route::get('result/paginate/{params?}', 'paginate');
    Route::post('result', 'store');
    Route::patch('result/{result}', 'update');
    Route::delete('result/{result}', 'delete');
});

Route::controller(ConfigController::class)->group(function () {
    Route::get('config/paginate/{params?}', 'paginate');
    Route::post('config', 'store');
    Route::get('config/{config?}', 'get');
    Route::patch('config/{config}', 'update');
    Route::delete('config/{config}', 'delete');
});

