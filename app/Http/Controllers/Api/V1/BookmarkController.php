<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bookmark;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class BookmarkController extends Controller
{
    public function bookmark(Request $request): JsonResponse
    {
        // Validate the incoming request
        $request->validate([
            "post_id" => "required|regex:/^@\w+$/", 
        ]);

        // Find the post based on the post_id
        $post = Post::where('post_id', $request->post_id)->firstOrFail();

        // Check if the bookmark already exists
        $bookmarkBefore = Bookmark::where('user_id', Auth::id())
            ->where('post_id', $post->id) // Match with the post's numeric ID
            ->first();

        if ($bookmarkBefore) {
            return response()->json([
                "status" => false,
                "message" => "You've added this blog to bookmark before",
            ]);
        }

        // Create the Bookmark
        $bookmark = Bookmark::create([
            "user_id" => Auth::id(),
            "post_id" => $post->id,
        ]);

        // Increment the bookmarks count
        $post->increment('bookmark_count');

        return response()->json([
            "status" => true,
            "message" => "Added to Bookmark Successfully",
            "bookmark" => $bookmark
        ]);
    }

        public function deletebookmark(Request $request): JsonResponse
    {
        $request->validate([
            'book_id' => 'required'
        ]);

        $bookId = $request->input('book_id'); 
        $book = Bookmark::find($bookId);

        if ($book) {
            // Find the related post
            $post = Post::find($book->post_id);

            if ($post) {
                // Decrement the likes count
                $post->decrement('bookmark_count');
                $post->save();
            }

            // Delete the book
            $book->delete();

            return response()->json([
                "status" => true,
                "message" => "Bookmark removed successfully"
            ]);
        } else {
            return response()->json([
                "status" => false,
                "message" => "Bookmark not found"
            ]);
        }
    }

    public function readbookmarks(Request $request): JsonResponse
    {
        $userId = Auth::user()->id;

        // Find all bookmarks associated with the post ID
        $bookmarks = Bookmark::with('post')
                    ->where('user_id', $userId)
                    ->get();

        // Return the bookmarks in a JSON response
        return response()->json([
            'status' => true,
            'message' => 'Bookmark data',
            'data' => $bookmarks,
        ]);
    }

        public function readbookmarkspost($postID): JsonResponse
    {
        // Find the post by post_id
        $post = Post::where('post_id', $postID)->first();

        // Ensure the post exists before proceeding
        if (!$post) {
            // Handle the case where the post is not found
            return response()->json([
                'status' => false,
                'message' => 'Post Not Found',
            ], 404);
        }

        // Find all bookmark associated with the post ID
        $bookmark = Bookmark::with('user')
                      ->where('post_id', $post->id)
                      ->get();

        // Check if bookmark exist
        if ($bookmark->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Oops! Empty',
            ]);
        }

        // Return the bookmark in a JSON response
        return response()->json([
            'status' => true,
            'message' => 'Bookmark data',
            'data' => $bookmark,
        ]);
    }

}
