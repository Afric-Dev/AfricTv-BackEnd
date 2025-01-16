<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\JsonResponse; 

//NOT IN USE
class SearchController extends Controller
{
        public function search(Request $request): JsonResponse
        {
            $user = User::with(['posts', 'educationals'])
                        ->where('unique_id', $uniqid)
                        ->first();

            // Check if user exists
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User Not Found',
                ]);
            }

            // Fetch posts and educational posts using the user's ID
            $userPosts = Post::where('user_id', $user->id)->get();
            $eduPosts = Educational::where('user_id', $user->id)->get();

            $postCount = $userPosts->count();
            $noofeduposts = $eduPosts->count();

            return response()->json([
                'status' => true,
                'message' => 'User data',
                'data' => $user,
            ]);
        }
}
