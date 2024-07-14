<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comments;
use Intervention\Image\Facades\Image;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use Illuminate\Support\Facades\Auth;

class CommentsController extends Controller
{
    public function comments(Request $request) {
        $request->validate([
            "post_id" => "required",
            "post_email" => "required",
            // "user_id" => "required",
            // "user_email" => "required|email",
            // "user_name" => "required",
            "unique_id" => "required",
            "comments" => "required",
            "comments_vid_path" => "nullable|mimes:mp4,avi,mov,wmv,flv",
            'comments_img_path' => 'array',
            'comments_img_path.*' => "nullable|image|max:2048",
            "comments_link" => "nullable", 
        ]);

        $imagePaths = [];
        if ($request->hasFile('comments_img_path')) {
            foreach ($request->file('comments_img_path') as $imageFile) {
                $imageSize = $imageFile->getSize();

                if ($imageSize > 2048000) { // 2MB in bytes
                    $image = Image::make($imageFile)->resize(500, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });

                    $imagePath = 'public/commentsimages/' . $imageFile->hashName();
                    $image->save(storage_path('app/' . $imagePath));
                } else {
                    $imagePath = $imageFile->store('public/commentsimages');
                    $imagePath = str_replace('public/', '', $imagePath);
                }
                $imagePaths[] = $imagePath;
            }
        }

        // Handle video upload
        if ($request->hasFile('comments_vid_path')) {
            $file = $request->file('comments_vid_path');

            // Check video duration
            $media = FFMpeg::open($file->getPathname());
            $duration = $media->getDurationInSeconds();

            if ($duration > 7200) { // 7200 seconds = 2 hours
                return response()->json([
                    'status' => false,
                    'message' => 'Video duration should not exceed 2 hours.'
                ]);
            }

            // Store the video
            $videoPath = $file->store('public/commentsvideos');
            
            // Save the path to the database
            $comment = new Comments();
            $comment->comments_vid_path = $videoPath;
            $videoPath = str_replace('public/', '', $videoPath);
            $comment->save();
          } else {
            $videoPath = "no video uploaded";
        } 

        $comments = Comments::create([
            "post_id" => $request->post_id,
            "post_email" => $request->post_email,
            "user_id" => Auth::user()->id
            "user_email" => Auth::user()->email,
            "user_name" => Auth::user()->name,
            "unique_id" => $request->unique_id,
            "comments" => $request->comments,
            "comments_vid_path" => $videoPath,
            "comments_img_path" => json_encode($imagePaths),
            "comments_link" => $request->comments_link,
        ]);

        return response()->json([
            "status" => true,
            "message" => "Comment Uploaded Successfully"
        ]);
    }


     public function updatecomments(Request $request)
        {
            $request->validate([
                "comment_id" => "required",
                "post_id" => "required",
                "post_email" => "required",
                "user_id" => "required",
                "user_email" => "required|email",
                "user_name" => "required",
                "unique_id" => "required",
                "comments" => "required",
                "comments_vid_path" => "nullable|mimes:mp4,avi,mov,wmv,flv",
                'comments_img_path' => 'array',
                'comments_img_path.*' => "nullable|image|max:2048",
                "comments_link" => "nullable", 
            ]);

            $commentId = $request->input('comment_id'); 
            $comment = Comments::find($commentId);

            if ($comment) {
                // Update comment properties
                $comment->comments = $request->comments;
                $comment->comments = $request->comments;
              
                // Handle image upload
                $imagePaths = [];

                if ($request->hasFile('comments_img_path')) {
                    foreach ($request->file('comments_img_path') as $imageFile) {
                        $imageSize = $imageFile->getSize();

                        if ($imageSize > 2048000) { // 2MB in bytes
                            $image = Image::make($imageFile)->resize(500, null, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            });

                            $imagePath = 'public/commentsimages/' . $imageFile->hashName();
                            $image->save(storage_path('app/' . $imagePath));
                        } else {
                            $imagePath = $imageFile->store('public/commentsimages');
                            $imagePath = str_replace('public/', '', $imagePath);
                        }
                        $imagePaths[] = $imagePath;
                    }
                } else {
                    $imagePaths[] = "no image uploaded";
                }



                // Handle video upload
                if ($request->hasFile('comments_vid_path')) {
                    $file = $request->file('comments_vid_path');

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
                    $videoPath = $file->store('public/commentsvideos');
                    
                    // Save the path to the database
                    $comment = new Comments();
                    $comment->comments_vid_path = $videoPath;
                    $videoPath = str_replace('public/', '', $videoPath);
                    $comment->save();
                  } else {
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
                    "message" => "Comment Updated Successfully"
                ]);
            } else {
                return response()->json([
                    "status" => false,
                    "message" => "Comment Not Found"
                ]);
            }
        }

         public function deletecomment(Request $request)
        {
            $request->validate([
                'comment_id' => 'required|integer'
            ]);

            $commentId = $request->input('comment_id');
            $comments = Comments::find($commentId);

            if ($comments) {
                $comments->delete();

                return response()->json([
                    "status" => true,
                    "message" => "Comment deleted successfully"
                ]);
            } else {
                return response()->json([
                    "status" => false,
                    "message" => "Comment not found"
                ]);
            }
        }

        public function readComment(Request $request)
        {
            $validated = $request->validate([
                'post_id' => 'required|string',
            ]);

            $postId = $validated['post_id'];

            // Find all comments associated with the post ID
            $comments = Comments::where('post_id', $postId)->get();

            // Return the comments in a JSON response
            return response()->json([
                'status' => true,
                'message' => 'Comments data',
                'data' => $comments,
            ]);
        }

}