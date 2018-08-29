<?php

use Illuminate\Http\Request;

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
//192.168.1.105/index.php/api/index/test
Route::get('/index/succ','\App\Http\Controllers\Api\IndexController@index');
Route::get('/index/fail','\App\Http\Controllers\Api\IndexController@fail');