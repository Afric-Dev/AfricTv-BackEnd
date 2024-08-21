<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Carbon\Carbon;

class TrendingController extends Controller
{
    public function trending() {
        // Set the timeframe for trending (posts from the last 24 hours)
        $timeframe = Carbon::now()->subHours(24);

        // Fetch posts within the specified timeframe
        $posts = Post::where('created_at', '>=', $timeframe)
            ->withCount(['likes', 'comments']) 
            ->get();

        // Calculate a trending score for each post
        $posts->map(function ($post) {
            // Formula for trending score, now including post_views
            $post->trending_score = ($post->likes_count * 1.5) + 
                                    ($post->comments_count * 2) + 
                                    ($post->post_views * 1);
            return $post;
        });

        // Sort posts by their trending score in descending order
        $trendingPosts = $posts->sortByDesc('trending_score')->take(10);

        return response()->json($trendingPosts);
    }
}
