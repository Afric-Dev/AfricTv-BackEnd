<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Controllers\Api\V1\NewPasswordController;
use App\Http\Controllers\Api\V1\UserCardDetails;
use App\Http\Controllers\Api\V1\FeedbackController;
use App\Http\Controllers\Api\V1\AdsPaymentController;
use App\Http\Controllers\Api\V1\AdsController;
use App\Http\Controllers\Api\V1\PostController;
//use App\Http\Controllers\Api\V1\FeedPostController; 
use App\Http\Controllers\Api\V1\SubscribtionController;
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
Route::get("IndividualProfile/{uniqid}", [ApiController::class, "IndividualProfile"]);
Route::post('forgot_password', [NewPasswordController::class, 'forgotPassword']);
Route::post('reset_password', [NewPasswordController::class, 'resetPassword']);
//Feed back Endpoint
Route::post('feedback', [FeedbackController::class, 'feedback']);
//Read BlogPost End Point
Route::get('readpost', [PostController::class, 'readpost']);
Route::get('postviews', [PostController::class, 'postviews']);
//Read Single BlogPost End Point
Route::get('readspecificpost/{uniqid}/{post_title}', [PostController::class, 'readspecificpost']);
//Blog Post view 
Route::post('ViewBlog/{id}', [PostController::class, 'ViewBlog']);
//Read FeedPost End Point
// Route::get('readfeedpost', [FeedPostController::class, 'readfeedpost']);
//Read Comment End Point
Route::get('readcomment', [CommentsController::class, 'readcomment']);
//Read Like End Point
Route::get('readlikes', [LikeController::class, 'readlikes']);
//Read Edu post 
Route::get("readedu", [EducationalController::class, "readedu"]);
Route::get("eduviews", [EducationalController::class, "eduviews"]);
Route::get('readspecificedupost/{uniqid}/{title}', [EducationalController::class, 'readspecificedupost']);
Route::post('ViewEdu/{id}', [EducationalController::class, 'ViewEdu']);
//Ads
Route::get("ads", [AdsController::class, "ads"]);
Route::post("AdsPerClicks", [AdsController::class, "AdsPerClicks"]);
//Subscribers
Route::get("viewsubscribers", [SubscribtionController::class, "viewsubscribers"]);

 
//Protected Route  
Route::group([
    "middleware" => ["auth:api"]
], function(){
    //THis is the Auth User Actions End point
    Route::get("profile", [ApiController::class, "profile"]);
    Route::post("logout", [ApiController::class, "logout"]);
    Route::put("updateprofile", [ApiController::class, "updateprofile"]);
    //THis is the Payments Endpoint
    Route::post("payment", [ApiController::class, "payment"]);
    Route::post("carddetails", [UserCardDetails::class, "carddetails"]);
    Route::post("adsPayment", [AdsPaymentController::class, "adsPayment"]);
    //Ads Endpoint
    Route::post("adActive", [AdsController::class, "adActive"]);
    Route::put("adInactive", [AdsController::class, "adInactive"]);
    Route::delete("deleteads", [AdsController::class, "deleteads"]);
    //THis is the BlogPost End Point
    Route::post("uploadpost", [PostController::class, "uploadpost"]);
    Route::put("updateposts/{id}", [PostController::class, "updateposts"]);
    Route::delete("deleteposts/{id}", [PostController::class, "deleteposts"]);
    //THis is the Subscribtion Endpint
    Route::post("subscribe", [SubscribtionController::class, "subscribe"]);
    Route::put("unsubscribe", [SubscribtionController::class, "unsubscribe"]);
    //THis is the FeedPost EndPoint
    // Route::post("feedposts", [FeedPostController::class, "feedposts"]);
    // Route::put("updatefeedposts/{id}", [FeedPostController::class, "updatefeedposts"]);
    // Route::delete("deletefeedposts/{id}", [FeedPostController::class, "deletefeedposts"]);
    //This is the Comment EndPoint
    Route::post("comments", [CommentsController::class, "comments"]);
    Route::put("updatecomments/{id}", [CommentsController::class, "updatecomments"]);
    Route::delete("deletecomment/{id}", [CommentsController::class, "deletecomment"]);
    //This is the Like EndPoint
    Route::post("like", [LikeController::class, "like"]);
    Route::put("unlike/{id}", [LikeController::class, "unlike"]); 

    //This is for educational end point
    Route::post("educational", [EducationalController::class, "educational"]);
    Route::delete("deleteedupost", [EducationalController::class, "deleteedupost"]);
});
