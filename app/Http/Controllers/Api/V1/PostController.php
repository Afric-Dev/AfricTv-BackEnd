<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
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
            "post_pdf_path" => "nullable|mimes:pdf,doc,docx",
            "post_song_path" => "nullable|mimes:mp3,wav,aac,flac",
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

                    $imagePath = 'public/images/' . $imageFile->hashName();
                    $image->save(storage_path('app/' . $imagePath));
                } else {
                    $imagePath = $imageFile->store('public/images');
                    $imagePath = str_replace('public/', '', $imagePath);
                }
                $imagePaths[] = $imagePath;
            }
        } else {
            $imagePaths[] = "no image uploaded";
        }

        if ($request->hasFile('cover_image')) {
            $imageFile = $request->file('cover_image');
            $imageSize = $imageFile->getSize();

            try {
                if ($imageSize > 2048000) { // 2MB in bytes
                    $image = Image::make($imageFile)->resize(500, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });

                    $coverImagePath = 'public/blogcoverimages/' . $imageFile->hashName();
                    $image->save(storage_path('app/' . $coverImagePath));
                } else {
                    $coverImagePath = $imageFile->store('public/blogcoverimages');
                    $coverImagePath = str_replace('public/', '', $coverImagePath);
                }
            } catch (\Exception $e) {
                return response()->json([
                    "status" => false,
                    "message" => "Error processing image: " . $e->getMessage()
                ], 500);
            }
        } else {
            return response()->json([
                "status" => false,
                "message" => "Cover Image Required"
            ], 400);
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
            $videoPath = $file->store('public/blogsvideos');
                
            // Save the path to the database
            $video = new Post();
            $video->post_vid_path = $videoPath;
            $videoPath = str_replace('public/', '', $videoPath);
            $video->save();
        }else {
            $videoPath = "no video uploaded";
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

        // Storing post data
        $post = Post::create([
            "user_id" => Auth::user()->id,
            "user_name" => Auth::user()->name,
            "post_id" => $postID,
            "unique_id" => Auth::user()->unique_id,
            "user_email" => Auth::user()->email,
            "cover_image" => $coverImagePath,
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

            // Handle cover image upload
            if ($request->hasFile('cover_image')) {
                $imageFile = $request->file('cover_image');
                $imageSize = $imageFile->getSize();

                try {
                    if ($imageSize > 2048000) { // 2MB in bytes
                        $image = Image::make($imageFile)->resize(500, null, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });

                        $coverImagePath = 'public/blogcoverimages/' . $imageFile->hashName();
                        $image->save(storage_path('app/' . $coverImagePath));
                    } else {
                        $coverImagePath = $imageFile->store('public/blogcoverimages');
                        $coverImagePath = str_replace('public/', '', $coverImagePath);
                    }
                    $post->cover_image = $coverImagePath;
                } catch (\Exception $e) {
                    return response()->json([
                        "status" => false,
                        "message" => "Error processing cover image: " . $e->getMessage()
                    ], 500);
                }
            }

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

                        $imagePath = 'public/images/' . $imageFile->hashName();
                        $image->save(storage_path('app/' . $imagePath));
                    } else {
                        $imagePath = $imageFile->store('public/images');
                        $imagePath = str_replace('public/', '', $imagePath);
                    }
                    $imagePaths[] = $imagePath;
                }
                $post->post_img_path = json_encode($imagePaths);
            }

           // Handle video upload
            if ($request->hasFile('post_vid_path')) {
                $videoPath = $request->file('post_vid_path')->store('public/videos');
                $videoPath = str_replace('public/', '', $videoPath);
            } else {
                $videoPath = "no file uploaded";
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
                $videoPath = $file->store('public/commentsvideos');
                
                // Save the path to the database
                $video = new Post();
                $video->post_vid_path = $videoPath;
                $videoPath = str_replace('public/', '', $videoPath);
                $video->save();
              } else {
                 $videoPath = "no video uploaded";
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
            $request->validate([
                'id' => 'required|integer'
            ]);

            $postId = $request->input('id');
            $post = Post::find($postId);

            if ($post) {
                $post->delete();

                return response()->json([
                    "status" => true,
                    "message" => "Post deleted successfully"
                ]);
            } else {
                return response()->json([
                    "status" => false,
                    "message" => "Post not found"
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

        public function readspecificpost($id) 
        {
            // Retrieve the post with the given ID
            $post = Post::find($id);

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