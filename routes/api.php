<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ContextualController;
use App\Http\Controllers\API\GlobalController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/contextual/isipho', [ContextualController::class, 'contextual_isipho']);
Route::post('/contextual/mamatheka', [ContextualController::class, 'contextual_mamatheka']);
Route::post('/contextual/funa', [ContextualController::class, 'contextual_funa']);
Route::post('/global/isipho', [GlobalController::class, 'global_isipho']);
Route::post('/global/mamatheka', [GlobalController::class, 'global_mamatheka']);
Route::post('/global/ubambo', [GlobalController::class, 'global_ubambo']);
Route::post('/global/ubambo/check', [GlobalController::class, 'global_ubambo_check']);
Route::post('/global/funa', [GlobalController::class, 'global_funa']);
