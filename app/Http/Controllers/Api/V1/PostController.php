<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
     public function posts(Request $request) 
    { 
        // Data Validation
        $request->validate([ 
            // "user_id" => "required",
            // "user_name" => "required",
            // "unique_id" => "required", 
            // "user_email" => "required|email",
            "cover_image" => 'required|image|max:2048',
            'post_img_path' => 'array',
            'post_img_path.*' => 'nullable|image|max:2048',
            'post_vid_path' => 'nullable|mimes:mp4,avi,mov,wmv,flv',
            // "post_pdf_path" => "nullable|mimes:pdf,doc,docx",
            // "post_song_path" => "nullable|mimes:mp3,wav,aac,flac",
            "category" => "required",
            "post_title" => "required",
            "PostbodyHtml" => "required",
            "postbodyJson" => "required",
            "postBodytext" => "required",
            "post_views" => "nullable",
            "link" => "nullable",
            "hashtags" => "nullable",
            "post_ending" => "nullable",
            "date" => "nullable|date",
        ]);

        $firstWord = strtok($request->user_name, ' ');
        // Generate a random four-digit number
        $randomNumber = rand(10000, 99999);

        $postID = '@' .$firstWord . $randomNumber;

        // Initialize an array to store image paths
        $imagePaths = [];
        // Check if the request has files under the 'post_img_path' key
        if ($request->hasFile('post_img_path')) {
            foreach ($request->file('post_img_path') as $file) {
                // Validate if the file is valid
                if ($file->isValid()) {
                    // Upload the file to Cloudinary
                    $uploadCloudinary = cloudinary()->upload(
                        $file->getRealPath(),
                        [
                            'folder' => 'africtv/blogs_images',
                            'resource_type' => 'auto',
                            'transformation' => [
                                'quality' => 'auto',
                                'fetch_format' => 'auto'
                            ]
                        ]
                    );

                    // Store the secure URL of the uploaded image
                    $imagePaths[] = $uploadCloudinary->getSecurePath();
                } else {
                    $imagePaths[] = "File is not valid";
                }
            }
        } else {
            $imagePaths[] = "No images uploaded";
        }


          //Handle blog post cover image (required)
          if ($request->hasFile('cover_image')) {
                $uploadCloudinary = cloudinary()->upload(
                    $request->file('cover_image')->getRealPath(),
                    [
                        'folder' => 'africtv/blogs_cover_images',
                        'resource_type' => 'auto',
                        'transformation' => [
                            'quality' => 'auto',
                            'fetch_format' => 'auto',
                        ]
                    ]
                );
                $coverimagePath = $uploadCloudinary->getSecurePath();
        } else {
            return response()->json([
                "status" => false,
                "message" => "Cover Image Required"
            ], 400);
        }

        // Function to get video duration
        function getVideoDuration($file)
        {
            return 0; 
        }

        if ($request->hasFile('post_vid_path')) {
            // Get the duration of the video
            $duration = getVideoDuration($request->file('post_vid_path'));

            // Validate video duration
            if ($duration > 7200) { // 7200 seconds = 2 hours
                return response()->json([
                    'status' => false,
                    'message' => 'Video duration should not exceed 2 hours.',
                ]);
            }

            try {
                $uploadCloudinary = cloudinary()->upload(
                    $request->file('post_vid_path')->getRealPath(),
                    [
                        'folder' => 'africtv/blogs_videos',
                        'resource_type' => 'auto',
                        'transformation' => [
                            'quality' => 'auto',
                            'fetch_format' => 'auto'
                        ]
                    ]
                );
                $videoPath = $uploadCloudinary->getSecurePath();
            } catch (\Exception $e) {
                // Handle upload error
                return response()->json([
                    'status' => false,
                    'message' => 'Video upload failed: ' . $e->getMessage(),
                ]);
            }
        } else {
            $videoPath = null;
        }


        //Handle document upload
        if ($request->hasFile('post_pdf_path')) {
            $docPath = $request->file('post_pdf_path')->store('public/documents');
            $docPath = str_replace('public/', '', $docPath);
        } else {
            $docPath = "no file uploaded";
        }

        //Handle song upload
        if ($request->hasFile('post_song_path')) {
            $songPath = $request->file('post_song_path')->store('public/songs');
            $songPath = str_replace('public/', '', $songPath);
        } else {
            $songPath = "no file uploaded";
        }

        // Storing post data
        $post = Post::create([
            "user_id" => Auth::user()->id,
            "user_name" => Auth::user()->name,
            "post_id" => $postID,
            "unique_id" => Auth::user()->unique_id,
            "user_email" => Auth::user()->email,
            "cover_image" => $coverimagePath,
            "post_img_path" => json_encode($imagePaths),
            "post_vid_path" => $videoPath,
            "post_pdf_path" => $docPath,
            "post_song_path" => $songPath,
            "category" => $request->category,
            "post_title" => $request->post_title,
            "PostbodyHtml" => $request->PostbodyHtml,
            "postbodyJson" => $request->postbodyJson,
            "postBodytext" => $request->postBodytext,
            "post_views" => $request->post_views ?? 0,
            "link" => $request->link,
            "hashtags" => $request->hashtags,
            "post_ending" => $request->post_ending,
            "date" => $request->date,
        ]);

        return response()->json([
            "status" => true,
            "message" => "BlogPost Uploaded Successfully"
        ]);
    }

      public function updateposts(Request $request)
    {
        $request->validate([
            "id" => "required|exists:posts,id",
            "cover_image" => 'nullable|image|max:2048',
            'post_img_path' => 'array',
            'post_img_path.*' => 'nullable|image|max:2048',
            'post_vid_path' => 'nullable|mimes:mp4,avi,mov,wmv,flv',
            "post_pdf_path" => "nullable|mimes:pdf,doc,docx",
            "post_song_path" => "nullable|mimes:mp3,wav,aac,flac",
            "category" => "required",
            "post_title" => "required",
            "PostbodyHtml" => "required",
            "postbodyJson" => "required",
            "postBodytext" => "required",
            "link" => "nullable",
            "hashtags" => "nullable",
            "post_ending" => "nullable",
        ]);

        $postId = $request->input('id'); 
        $post = Post::find($postId);

        if ($post) {
            // Update post properties
            $post->post_title = $request->post_title;
            $post->category = $request->category;
            $post->link = $request->link;
            $post->PostbodyHtml = $request->PostbodyHtml;
            $post->postbodyJson = $request->postbodyJson;
            $post->postBodytext = $request->postBodytext;
            $post->hashtags = $request->hashtags;
            $post->post_ending = $request->post_ending;

        // Initialize an array to store image paths
        $imagePaths = [];
        // Check if the request has files under the 'post_img_path' key
        if ($request->hasFile('post_img_path')) {
            foreach ($request->file('post_img_path') as $file) {
                // Validate if the file is valid
                if ($file->isValid()) {
                    // Upload the file to Cloudinary
                    $uploadCloudinary = cloudinary()->upload(
                        $file->getRealPath(),
                        [
                            'folder' => 'africtv/blogs_images',
                            'resource_type' => 'auto',
                            'transformation' => [
                                'quality' => 'auto',
                                'fetch_format' => 'auto'
                            ]
                        ]
                    );

                    // Store the secure URL of the uploaded image
                    $imagePaths[] = $uploadCloudinary->getSecurePath();
                } else {
                    $imagePaths[] = "File is not valid";
                }
            }
        } else {
            $imagePaths[] = "No images uploaded";
        }


          //Handle blog post cover image (required)
          if ($request->hasFile('cover_image')) {
                $uploadCloudinary = cloudinary()->upload(
                    $request->file('cover_image')->getRealPath(),
                    [
                        'folder' => 'africtv/blogs_cover_images',
                        'resource_type' => 'auto',
                        'transformation' => [
                            'quality' => 'auto',
                            'fetch_format' => 'auto'
                        ]
                    ]
                );
                $coverimagePath = $uploadCloudinary->getSecurePath();
        } else {
            return response()->json([
                "status" => false,
                "message" => "Cover Image Required"
            ], 400);
        }

        // Function to get video duration
        function getVideoDuration($file)
        {
            return 0; 
        }

        if ($request->hasFile('post_vid_path')) {
            // Get the duration of the video
            $duration = getVideoDuration($request->file('post_vid_path'));

            // Validate video duration
            if ($duration > 7200) { // 7200 seconds = 2 hours
                return response()->json([
                    'status' => false,
                    'message' => 'Video duration should not exceed 2 hours.',
                ]);
            }

            try {
                $uploadCloudinary = cloudinary()->upload(
                    $request->file('post_vid_path')->getRealPath(),
                    [
                        'folder' => 'africtv/blogs_videos',
                        'resource_type' => 'auto',
                        'transformation' => [
                            'quality' => 'auto',
                            'fetch_format' => 'auto'
                        ]
                    ]
                );
                $videoPath = $uploadCloudinary->getSecurePath();
            } catch (\Exception $e) {
                // Handle upload error
                return response()->json([
                    'status' => false,
                    'message' => 'Video upload failed: ' . $e->getMessage(),
                ]);
            }
        } else {
            $videoPath = null;
        }

            // Handle document upload
            if ($request->hasFile('post_pdf_path')) {
                $docPath = $request->file('post_pdf_path')->store('public/documents');
                $docPath = str_replace('public/', '', $docPath);
            } else {
                $docPath = "no file uploaded";
            }

            // Handle song upload
            if ($request->hasFile('post_song_path')) {
                $songPath = $request->file('post_song_path')->store('public/songs');
                $songPath = str_replace('public/', '', $songPath);
            } else {
                $songPath = "no file uploaded";
            }
            // Save the updated post
            $post->save();

            // Send mail if it was successful (commented out)
            // Mail::to($request->user_email)->send(new ProfileUpdateMail($post));

            return response()->json([
                "status" => true,
                "message" => "BlogPost Updated Successfully"
            ]);
        } else {
            return response()->json([
                "status" => false,
                "message" => "BlogPost Not Found"
            ]);
        }
    }


        public function deleteposts(Request $request)
        {
            // Validate the request
            $request->validate([
                'id' => 'required|integer'
            ]);

            // Get the post that has this id
            $postId = $request->input('id');
            $post = Post::find($postId);

            // Check if the post exists
            if (!$post) {
                return response()->json([
                    "status" => false,
                    "message" => "Post not found"
                ]);
            }

            // Check if the authenticated user is the owner of the post
            if (Auth::user()->id === $post->user_id) {
                // Delete the post
                $post->delete();

                return response()->json([
                    "status" => true,
                    "message" => "Post deleted successfully"
                ]);
            } else {
                return response()->json([
                    "status" => false,
                    "message" => "You are not permitted to delete this post"
                ]);
            }
        }


        public function readpost()
        {
            $posts = Post::inRandomOrder()->get();
            $postCount = $posts->count();

            return response()->json([
                'status' => true,
                'message' => 'Post data',
                'data' => $posts,
                'count' => $postCount,
            ]);
        }

        public function readspecificpost($post_title) 
        {
            // Retrieve the post with the given post_title
            $post = Post::where('post_title', $post_title)->first();

            // Check if post exists
            if (!$post) {
                return response()->json([
                    'status' => false,
                    'message' => 'Post Not Found',
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Post data',
                'data' => $post,
            ]);
        }

}