<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\Models\Feedposts;
 
class FeedPostController extends Controller
{
    public function feedposts(Request $request)
    {
        // Data Validation
        $request->validate([
            "avatar_path" => "nullable",
            "user_id" => "required",
            "user_name" => "required",
            "unique_id" => "required",
            "user_email" => "required|email",
            'post_img_path' => 'array',
            'post_img_path.*' => 'nullable|image|max:2048',
            'post_vid_path' => 'nullable|mimes:mp4,avi,mov,wmv,flv',
            "post_pdf_path" => "nullable|mimes:pdf,doc,docx",
            "post_song_path" => "nullable|mimes:mp3,wav,aac,flac",
            "link" => "nullable",
            "hashtags" => "nullable",
            "user" => "nullable",
            "post_body" => "required",
            "post_views" => "nullable",
            "date" => "nullable|date",
        ]);

         $firstWord = strtok($request->user_name, ' ');
            // Generate a random four-digit number
            $randomNumber = rand(10000, 99999);

            $postID = '@' .$firstWord . $randomNumber;

         // Handle image upload and resizing
        $imagePaths = [];
        if ($request->hasFile('post_img_path')) {
            foreach ($request->file('post_img_path') as $imageFile) {
                $imageSize = $imageFile->getSize();

                if ($imageSize > 2048000) { // 2MB in bytes
                    $image = Image::make($imageFile)->resize(500, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });

                    $imagePath = 'public/feedimages/' . $imageFile->hashName();
                    $image->save(storage_path('app/' . $imagePath));
                } else {
                    $imagePath = $imageFile->store('public/feedimages');
                    $imagePath = str_replace('public/', '', $imagePath);
                }
                $imagePaths[] = $imagePath;
            }
        } else {
            $imagePaths[] = "no image uploaded";
        }




        // Handle video upload
        if ($request->hasFile('post_vid_path')) {
            $file = $request->file('post_vid_path');

            // Check video duration
            $media = FFMpeg::open($file->getPathname());
            $duration = $media->getDurationInSeconds();

            if ($duration > 7200) { // 7200 seconds = 2 hours
                return response()->json([
                'status' => false,
                'message' => 'Video duration should not exceed 2 hours.',
                ]);
            }

            // Store the video
            $videoPath = $file->store('public/feedvideos');
                
            // Save the path to the database
            $video = new Post();
            $video->post_vid_path = $videoPath;
            $videoPath = str_replace('public/', '', $videoPath);
            $video->save();
        }else {
            $videoPath = "no voideo uploaded";
        } 

        // Handle document upload
        // if ($request->hasFile('post_pdf_path')) {
        //     $docPath = $request->file('post_pdf_path')->store('public/feeddocuments');
        // } else {
        //     $docPath = "no file uploaded";
        // }

        // Handle song upload
        // if ($request->hasFile('post_song_path')) {
        //     $songPath = $request->file('post_song_path')->store('public/feedsongs');
        // } else {
        //     $songPath = "no file uploaded";
        // }

        // Storing post data
        $post = Feedposts::create([
            "avatar_path" => $request->avatar_path,
            "post_id" => $postID,
            "user_id" => $request->user_id,
            "user_name" => $request->user_name,
            "unique_id" => $request->unique_id,
            "user_email" => $request->user_email,
            "post_img_path" => json_encode($imagePaths),
            "post_vid_path" => $videoPath,
            // "post_pdf_path" => $docPath,
            // "post_song_path" => $songPath,
            "link" => $request->link,
            "user" => $request->user,
            "hashtags" => $request->hashtags,
            "post_body" => $request->post_body,
            "post_views" => $request->post_views ?? 0,
            "date" => $request->date,
        ]);

        return response()->json([
            "status" => true,
            "message" => "FeedPost Uploaded Successfully"
        ]);
    }
   public function updatefeedposts(Request $request)
{
    $request->validate([
        "avatar_path" => "nullable",
            "post_id" => "required",
            'post_img_path' => 'array',
            'post_img_path.*' => 'nullable|image|max:2048',
            'post_vid_path' => 'nullable|mimes:mp4,avi,mov,wmv,flv',
            "post_pdf_path" => "nullable|mimes:pdf,doc,docx",
            "post_song_path" => "nullable|mimes:mp3,wav,aac,flac",
            "link" => "nullable",
            "hashtags" => "nullable",
            "user" => "nullable",
            "post_body" => "required",
            "post_views" => "nullable",
            "date" => "nullable|date",
    ]);

            $postId = $request->input('post_id'); 
            $post = Feedposts::find($postId);

            if ($post) {
                // Update post properties
                $post->post_body = $request->post_body;
                $post->user = $request->user;
                $post->link = $request->link;
                $post->hashtags = $request->hashtags;
              
                // Handle image upload
                $imagePaths = [];

                if ($request->hasFile('post_img_path')) {
                    foreach ($request->file('post_img_path') as $imageFile) {
                        $imageSize = $imageFile->getSize();

                        if ($imageSize > 2048000) { // 2MB in bytes
                            $image = Image::make($imageFile)->resize(500, null, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            });

                            $imagePath = 'public/feedimages/' . $imageFile->hashName();
                            $image->save(storage_path('app/' . $imagePath));
                        } else {
                            $imagePath = $imageFile->store('public/feedimages');
                            $imagePath = str_replace('public/', '', $imagePath);
                        }
                        $imagePaths[] = $imagePath;
                    }
                } else {
                    $imagePaths[] = "no image uploaded";
                }

                  // Handle video upload
                    if ($request->hasFile('post_vid_path')) {
                        $file = $request->file('post_vid_path');

                        // Check video duration
                        $media = FFMpeg::open($file->getPathname());
                        $duration = $media->getDurationInSeconds();

                        if ($duration > 7200) { // 7200 seconds = 2 hours
                            return response()->json([
                            'status' => false,
                            'message' => 'Video duration should not exceed 2 hours.',
                            ]);
                        }

                        // Store the video
                        $videoPath = $file->store('public/feedvideos');
                            
                        // Save the path to the database
                        $video = new Post();
                        $video->post_vid_path = $videoPath;
                        $videoPath = str_replace('public/', '', $videoPath);
                        $video->save();
                    }else {
                        $videoPath = "no video uploaded";
                    } 

                // Handle document upload
                // if ($request->hasFile('post_pdf_path')) {
                //     $docPath = $request->file('post_pdf_path')->store('public/documents');
                //     $post->post_pdf_path = $docPath;
                // }

                // // Handle song upload
                // if ($request->hasFile('post_song_path')) {
                //     $songPath = $request->file('post_song_path')->store('public/songs');
                //     $post->post_song_path = $songPath;
                // }

                // Save the updated post
                $post->save();

                // // Send mail if it was successful
                // Mail::to($request->user_email)->send(new ProfileUpdateMail($post));

                return response()->json([
                    "status" => true,
                    "message" => "FeedPost Updated Successfully"
                ]);
            } else {
                return response()->json([
                    "status" => false,
                    "message" => "FeedPost Not Found"
                ]);
            }
        }

       public function deletefeedposts(Request $request)
        {
            $request->validate([
                'post_id' => 'required|integer'
            ]);

            $postId = $request->input('post_id');
            $post = Feedposts::find($postId);

            if ($post) {
                $post->delete();

                return response()->json([
                    "status" => true,
                    "message" => "FeedPost deleted successfully"
                ]);
            } else {
                return response()->json([
                    "status" => false,
                    "message" => "FeedPost not found"
                ]);
            }
        }

        public function readfeedpost()
        {
            $posts = Feedposts::all(); // Retrieve all posts from the database

            return response()->json([
                'status' => true,
                'message' => 'FeedPost data',
                'data' => $posts,
            ]);
        }



}