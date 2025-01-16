<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Educational;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::orderBy('created_at', 'desc')
                ->with('user')
                ->paginate(20);

        return view('admin.posts', compact('posts'));
    }

    public function updateStatus($id)
    {
        // Find the post by ID
        $post = Post::findOrFail($id);

        // Toggle the post's status between 'ACTIVE' and 'BANNED'
        $post->is_status = ($post->is_status == 'ACTIVE') ? 'INACTIVE' : 'ACTIVE';
        $post->save();

        // Return a response (redirect back with a success message)
        return redirect()->back()->with('message', 'Post status updated successfully');
    }

    public function updateVideoStatus($id)
    {
        // Find the post by ID
        $post = Educational::findOrFail($id);

        // Toggle the post's status between 'ACTIVE' and 'BANNED'
        $post->is_status = ($post->is_status == 'ACTIVE') ? 'INACTIVE' : 'ACTIVE';
        $post->save();

        // Return a response (redirect back with a success message)
        return redirect()->back()->with('message', 'Post status updated successfully');
    }


    public function videos(Request $request)
    {
        $posts = Educational::orderBy('created_at', 'desc')
                ->with('user')
                ->paginate(20);

        return view('admin.videos', compact('posts'));
    }
}
