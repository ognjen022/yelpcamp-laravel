<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('register', 'UserController@register');
Route::post('login', 'UserController@authenticate');
Route::get("/me", "UserController@getAuthenticatedUser");

Route::get("/comments", "CommentController@index");
Route::get("/comments/{id}", "CommentController@show");

Route::get("/campgrounds", "CampgroundController@index");
Route::get("/campgrounds/{id}", "CampgroundController@show");

Route::group(['middleware' => ['jwt.verify']], function() {
    // Comments routes
    Route::post("/comments", "CommentController@store");
    Route::put("/comments/{id}", "CommentController@update");
    Route::delete("/comments/{id}", "CommentController@destroy");
    Route::post("/comments/{id}/like", "CommentController@likeComment");
    Route::post("/comments/{id}/unlike", "CommentController@unlikeComment");

    //Campgrounds routes
    Route::post("/campgrounds", "CampgroundController@store");
    Route::put("/campgrounds/{id}", "CampgroundController@update");
    Route::delete("/campgrounds/{id}", "CampgroundController@destroy");
    Route::post("/campgrounds/rate", "CampgroundController@rateCampground");
});

Route::get("/campgrounds/{id}/user", "CampgroundController@showUser");
Route::get("/campgrounds/{id}/comments", "CampgroundController@showComments");
Route::get("/comments/{id}/user", "CommentController@showUser");
Route::get("/comments/{id}/campground", "CommentController@showCampground");
