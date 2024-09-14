<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bookmark;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;


class BookmarkController extends Controller
{
    public function bookmark(Request $request) {
        // Validate the incoming request
        $request->validate([
            "post_id" => "required|regex:/^@\w+$/", 
        ]);

        // Find the post based on the post_id format
        $post = Post::where('post_id', $request->post_id)->firstOrFail();

        // Create the Bookmark
        $bookmark = Bookmark::Create(
            [
                "user_id" => Auth::user()->id,
                "post_id" => $post->id,
            ],
        );

        // Increment the bookmarks count
        $post->increment('bookmark_count');
        $post->save();

        return response()->json([
            "status" => true,
            "message" => "Added to Bookmark Successfully",
            "bookmark" => $bookmark
        ]);
    }

        public function deletebookmark(Request $request)
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

    public function readbookmarks(Request $request)
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


}
