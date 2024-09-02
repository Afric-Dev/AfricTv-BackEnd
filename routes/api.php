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
use App\Http\Controllers\Api\V1\TrendingController;
use App\Http\Controllers\Api\V1\BookmarkController;
use App\Http\Controllers\Api\V1\EduVoteController;
use App\Http\Controllers\Api\V1\EduThoughtsController;
use App\Http\Controllers\Api\V1\SearchController;

// Route::get('/user', function (Request $request) {
//     return $request->user(); 
// })->middleware('auth:api');

//Open Route
//Auth
Route::post('register', [ApiController::class, 'register']);
Route::post("login", [ApiController::class, "login"]);
Route::get("IndividualProfile/{uniqid}", [ApiController::class, "IndividualProfile"]);
Route::post('forgot_password', [NewPasswordController::class, 'forgotPassword']);
Route::post('reset_password', [NewPasswordController::class, 'resetPassword']);
//Feed back Endpoint
Route::post('feedback', [FeedbackController::class, 'feedback']);
//Read BlogPost
Route::get('readpost', [PostController::class, 'readpost']);
Route::get('postviews', [PostController::class, 'postviews']);
//Read Single BlogPost
Route::get('readspecificpost/{uniqid}/{post_title}', [PostController::class, 'readspecificpost']);
//Read FeedPost
// Route::get('readfeedpost', [FeedPostController::class, 'readfeedpost']);
//Read Comment (Thoughts)
Route::get('readcomment/{postID}', [CommentsController::class, 'readcomment']);
//Read Like (Vote)
Route::get('readlikes/{postID}', [LikeController::class, 'readlikes']);
//Read Edu post 
Route::get("readedu", [EducationalController::class, "readedu"]);
Route::post("eduviews", [EducationalController::class, "eduviews"]);
Route::get('readspecificedupost/{uniqid}/{title}', [EducationalController::class, 'readspecificedupost']);
//Educational (Reactions)
Route::get('readeduvotes/{eduID}', [EduVoteController::class, 'readeduvotes']);
Route::get('readeduthoughts/{postID}', [EduThoughtsController::class, 'readeduthoughts']);
// Route::post('ViewEdu/{id}', [EducationalController::class, 'ViewEdu']);
//Ads
Route::get("ads", [AdsController::class, "ads"]);
Route::post("AdsPerClicks", [AdsController::class, "AdsPerClicks"]);
//Subscribers
Route::get("viewsubscribers", [SubscribtionController::class, "viewsubscribers"]);
//Trending
Route::get("trending", [TrendingController::class, "trending"]);


 
//Protected Route  
Route::group([
    "middleware" => ["auth:api"]
], function(){
    //Auth User Actions
    Route::get("profile", [ApiController::class, "profile"]);
    Route::post("logout", [ApiController::class, "logout"]);
    Route::post("updateprofile", [ApiController::class, "updateprofile"]);
    //Payments (Ads)
    Route::post("payment", [ApiController::class, "payment"]);
    Route::post("carddetails", [UserCardDetails::class, "carddetails"]);
    Route::post("adsPayment", [AdsPaymentController::class, "adsPayment"]);
    //Ads
    Route::post("adActive", [AdsController::class, "adActive"]);
    Route::post("adInactive", [AdsController::class, "adInactive"]);
    Route::post("deleteads", [AdsController::class, "deleteads"]);
    //BlogPost
    Route::post("uploadpost", [PostController::class, "uploadpost"]);
    // Route::post("updateposts/{id}", [PostController::class, "updateposts"]);
    Route::post("deleteposts", [PostController::class, "deleteposts"]);
    //Subscribtion
    Route::post("subscribe", [SubscribtionController::class, "subscribe"]);
    Route::post("unsubscribe", [SubscribtionController::class, "unsubscribe"]);
    //THis is the FeedPost EndPoint
    // Route::post("feedposts", [FeedPostController::class, "feedposts"]);
    // Route::post("updatefeedposts/{id}", [FeedPostController::class, "updatefeedposts"]);
    // Route::delete("deletefeedposts/{id}", [FeedPostController::class, "deletefeedposts"]);
    //Comment
    Route::post("comments", [CommentsController::class, "comments"]);
    // Route::post("updatecomments/{id}", [CommentsController::class, "updatecomments"]);
    Route::post("deletecomment", [CommentsController::class, "deletecomment"]);
    //Like (Vote)
    Route::post("like", [LikeController::class, "like"]);
    Route::post("unlike", [LikeController::class, "unlike"]); 
    //Educational 
    Route::post("educational", [EducationalController::class, "educational"]);
    Route::post("deleteedupost", [EducationalController::class, "deleteedupost"]);
    //Educational (Reactions) 
    Route::post("eduvote", [EduVoteController::class, "eduvote"]);
    Route::post("uneduvote", [EduVoteController::class, "uneduvote"]); 
    Route::post("eduthoughts", [EduThoughtsController::class, "eduthoughts"]); 
    Route::post("deleteeduthoughts", [EduThoughtsController::class, "deleteeduthoughts"]); 
    //Bookmark 
    Route::post("bookmark", [BookmarkController::class, "bookmark"]);
    Route::post("deletebookmark", [BookmarkController::class, "deletebookmark"]);
    Route::get("readbookmarks", [BookmarkController::class, "readbookmarks"]);
});
