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

Route::post("generateToken", "UserController@login");
Route::post("register", "UserController@register");
Route::get("profile", "UserController@getAuthenticatedUser");

Route::middleware("auth:api")->get("/user", function (Request $request) {
    return $request->user();
});
?>
