<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Controllers\Api\V1\ForgotPasswordController;
// use App\Http\Controllers\Api\V1\UserCardDetails;
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
use App\Http\Controllers\Api\V1\AIController;
use App\Http\Controllers\Api\V1\NotificationsController;
use App\Http\Controllers\Api\V1\MusicController;
use App\Http\Controllers\Api\V1\UploadMediaController;

// Route::get('/user', function (Request $request) {
//     return $request->user(); 
// })->middleware('auth:api');

//Open Route
//Auth
Route::post('register', [ApiController::class, 'register']);
Route::post("login", [ApiController::class, "login"]);
Route::get("IndividualProfile/{uniqid}", [ApiController::class, "IndividualProfile"]);
//Forget and Rest password
Route::post('password/forgot', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('password/reset', [ForgotPasswordController::class, 'reset']);
//Feedback 
Route::post('feedback', [FeedbackController::class, 'feedback']);
//Read BlogPost
Route::get('readpost', [PostController::class, 'readpost']);
Route::post('postviews', [PostController::class, 'postviews']);
//Read Single BlogPost
Route::get('readspecificpost/{post_id}/{post_title}', [PostController::class, 'readspecificpost']);
//Read FeedPost
// Route::get('readfeedpost', [FeedPostController::class, 'readfeedpost']);
//Read Comment (Thoughts)
Route::get('readcomment/{postID}', [CommentsController::class, 'readcomment']);
Route::get('readinndercomment/{commentID}', [CommentsController::class, 'readInnerComment']);
//Read Like (Vote)
Route::get('readlikes/{postID}', [LikeController::class, 'readlikes']);
//Read Edu post 
Route::get("readedu", [EducationalController::class, "readedu"]);
Route::post("eduviews", [EducationalController::class, "eduviews"]);
Route::get('readspecificedupost/{edu_id}/{title}', [EducationalController::class, 'readspecificedupost']);
//Educational (Reactions)
Route::get('readeduvotes/{eduID}', [EduVoteController::class, 'readeduvotes']);
Route::get('readeduthoughts/{postID}', [EduThoughtsController::class, 'readeduthoughts']);
// Route::post('ViewEdu/{id}', [EducationalController::class, 'ViewEdu']);
//Ads
Route::get("ads", [AdsController::class, "ads"]);
Route::post("AdsPerClicks", [AdsController::class, "AdsPerClicks"]);
//Music
Route::get('musicSearch', [MusicController::class, 'search']);
Route::post('getDetails/{trackId}', [MusicController::class, 'artist']);
//Trending
Route::get("trending", [TrendingController::class, "trending"]);
Route::get("search/{searchQuery}", [TrendingController::class, "search"]);


 
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
    Route::post("UserSetAdsInactive", [AdsController::class, "UserSetAdsInactive"]);
    Route::post("UserSetAdsActive", [AdsController::class, "UserSetAdsActive"]);
    Route::post("adInactive", [AdsController::class, "adInactive"]);
    Route::post("deleteads", [AdsController::class, "deleteads"]); 
    Route::post('updateAd/{adId}', [AdsController::class, 'updateAd']);
    //BlogPost
    Route::post("uploadpost", [PostController::class, "uploadpost"]);
    //Route::post("updateposts/{id}", [PostController::class, "updateposts"]);
    Route::post("deleteposts", [PostController::class, "deleteposts"]);
    //Subscribtion
    Route::post("subscribe", [SubscribtionController::class, "subscribe"]);
    Route::post("unsubscribe", [SubscribtionController::class, "unsubscribe"]);
    Route::get("subscribers", [SubscribtionController::class, "subscribers"]);
    Route::get("viewsubscribers/{uniqid}", [SubscribtionController::class, "viewsubscribers"]);
    //THis is the FeedPost EndPoint
    // Route::post("feedposts", [FeedPostController::class, "feedposts"]);
    // Route::post("updatefeedposts/{id}", [FeedPostController::class, "updatefeedposts"]);
    // Route::delete("deletefeedposts/{id}", [FeedPostController::class, "deletefeedposts"]);
    //Comment  (Thought)
    Route::post("comments", [CommentsController::class, "comments"]);
    // Route::post("updatecomments/{id}", [CommentsController::class, "updatecomments"]);
    Route::post("deletecomment", [CommentsController::class, "deletecomment"]);
    Route::post("deleteinnercomment", [CommentsController::class, "deleteInnerComment"]);
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
    //AI 
    Route::post("ai", [AIController::class, "ai"]);
    //Notification
    Route::get("notifications", [NotificationsController::class, "index"]);
    Route::get("markAsRead/{id}", [NotificationsController::class, "markAsRead"]);
    Route::get("markAsUnRead/{id}", [NotificationsController::class, "markAsUnRead"]);
    Route::get("notificationDelete/{id}", [NotificationsController::class, "destroy"]);
    //Media's (Blogs and Educational)
    Route::post("uploadImage", [UploadMediaController::class, "uploadImage"]);
    Route::post("uploadVideo", [UploadMediaController::class, "uploadVideo"]);
    Route::post("deleteImage", [UploadMediaController::class, "deleteImage"]);
    Route::post("deleteVideo", [UploadMediaController::class, "deleteVideo"]);

});
 