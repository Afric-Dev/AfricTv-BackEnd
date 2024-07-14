<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Controllers\Api\V1\NewPasswordController;
use App\Http\Controllers\Api\V1\UserCardDetails;
use App\Http\Controllers\Api\V1\FeedbackController;
use App\Http\Controllers\Api\V1\AdsPayment;
use App\Http\Controllers\Api\V1\AdsPaymentController;
use App\Http\Controllers\Api\V1\PostController;
//use App\Http\Controllers\Api\V1\FeedPostController; 
use App\Http\Controllers\Api\V1\SubscribtionController;
use App\Http\Controllers\Api\V1\UnsubscribeController;
use App\Http\Controllers\Api\V1\CommentsController; 
use App\Http\Controllers\Api\V1\LikeController;
use App\Http\Controllers\Api\V1\EducationalController;

// Route::get('/user', function (Request $request) {
//     return $request->user(); 
// })->middleware('auth:api');

//Open Route
//Auth Endpoint
Route::post('register', [ApiController::class, 'register']);
Route::post("login", [ApiController::class, "login"]);
Route::post('forgot_password', [NewPasswordController::class, 'forgotPassword']);
Route::post('reset_password', [NewPasswordController::class, 'resetPassword']);
//Feed back Endpoint
Route::post('feedback', [FeedbackController::class, 'feedback']);
//Read BlogPost End Point
Route::get('readpost', [PostController::class, 'readpost']);
//Read Single BlogPost End Point
Route::get('readspecificpost/{id}', [PostController::class, 'readspecificpost']);
//Read FeedPost End Point
// Route::get('readfeedpost', [FeedPostController::class, 'readfeedpost']);
//Read Comment End Point
Route::get('readcomment', [CommentsController::class, 'readcomment']);
//Read Like End Point
Route::get('readlikes', [LikeController::class, 'readlikes']);
//Trending End Point


 
//Protected Route  
Route::group([
    "middleware" => ["auth:api"]
], function(){
    //THis is the Auth User Actions End point
    Route::get("profile", [ApiController::class, "profile"]);
    Route::post("logout", [ApiController::class, "logout"]);
    Route::put("updateprofile/{id}", [ApiController::class, "updateprofile"]);
    //THis is the Payments Endpoint
    Route::post("payment", [ApiController::class, "payment"]);
    Route::post("carddetails", [UserCardDetails::class, "carddetails"]);
    Route::post("adsPayment", [AdsPaymentController::class, "adsPayment"]);
    //THis is the BlogPost End Point
    Route::post("posts", [PostController::class, "posts"]);
    Route::put("updateposts/{id}", [PostController::class, "updateposts"]);
    Route::delete("deleteposts/{id}", [PostController::class, "deleteposts"]);
    //THis is the Subscribtion Endpint
    Route::post("subscribtion", [SubscribtionController::class, "subscribtion"]);
    Route::get("viewsubscribtion", [SubscribtionController::class, "viewsubscribtion"]);
    Route::put("unsubscribe", [UnsubscribeController::class, "unsubscribe"]);
    //THis is the FeedPost EndPoint
    // Route::post("feedposts", [FeedPostController::class, "feedposts"]);
    // Route::put("updatefeedposts/{id}", [FeedPostController::class, "updatefeedposts"]);
    // Route::delete("deletefeedposts/{id}", [FeedPostController::class, "deletefeedposts"]);
    //This is the Comment EndPoint
    Route::post("comments", [CommentsController::class, "comments"]);
    Route::put("updatecomments/{id}", [CommentsController::class, "updatecomments"]);
    Route::put("deletecomment/{id}", [CommentsController::class, "deletecomment"]);
    //This is the Like EndPoint
    Route::post("like", [LikeController::class, "like"]);
    Route::put("unlike/{id}", [LikeController::class, "unlike"]); 

    //This is for educational end point
    Route::post("educational", [EducationalController::class, "educational"]);
});
