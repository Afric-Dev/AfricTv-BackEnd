<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use App\Models\Ads;
use App\Models\Educational;
use Carbon\Carbon;

class TrendingController extends Controller
{


    public function trending()
    {
        // Timeframe for trending (posts from the last 24 hours)
        $timeframe = Carbon::now()->subHours(48);

        // Fetch blogs within the specified timeframe and get categories
        $posts = Post::where('created_at', '>=', $timeframe)->get();

        // Create a collection to store category counts
        $categoryCounts = collect();

        // Count the occurrences of each category
        foreach ($posts as $post) {
            $categories = explode(',', $post->category);
            foreach ($categories as $category) {
                $category = trim($category); // Remove any extra spaces
                if ($category !== '') {
                    $categoryCounts->put($category, $categoryCounts->get($category, 0) + 1);
                }
            }
        }

        // Sort categories by their count in descending order
        $trendingCategories = $categoryCounts->sortDesc()->take(20);

        return response()->json([
            "status" => true,
            "message" => "Trending categories",
            "data" => $trendingCategories,
        ]);
    }


    public function search($searchQuery)
    {
        // Search Users by name, unique_id, or email
        $users = User::where('name', 'LIKE', "%{$searchQuery}%")
            ->orWhere('unique_id', 'LIKE', "%{$searchQuery}%")
            ->orWhere('email', 'LIKE', "%{$searchQuery}%")
            ->get();

        // Search and calculate trending score for Educational model
        $educationals = Educational::where('title', 'LIKE', "%{$searchQuery}%")
            ->orWhere('description', 'LIKE', "%{$searchQuery}%")
            ->with('user') 
            ->get()
            ->map(function ($edu) {
                // Trending score formula: edu_views * 2 + vote_count * 1.5 + thoughts_count * 1.5
                $edu->trending_score = ($edu->edu_views * 2) +
                                       ($edu->vote_count * 1.5) +
                                       ($edu->thoughts_count * 1.5);
                return $edu;
            })
            ->sortByDesc('trending_score') // Sort by trending score
            ->sortByDesc('created_at');    // Then sort by created_at

        // Search and calculate trending score for Posts
        $posts = Post::where('post_title', 'LIKE', "%{$searchQuery}%")
            ->orWhere('PostbodyHtml', 'LIKE', "%{$searchQuery}%")
            ->with('user') 
            ->get()
            ->map(function ($post) {
                // Trending score formula: likes * 1.5 + comments * 1.5 + views * 2
                $post->trending_score = ($post->likes_count * 1.5) +
                                        ($post->comments_count * 1.5) +
                                        ($post->post_views * 2);
                return $post;
            })
            ->sortByDesc('trending_score') // Sort by trending score
            ->sortByDesc('created_at');    // Then sort by created_at

            // Search ads Posts
            $ads = Ads::where('title', 'LIKE', "%{$searchQuery}%")
            ->orWhere('description', 'LIKE', "%{$searchQuery}%")
            ->get();

        return response()->json([
            "status" => true,
            "message" => "Search results",
            "data" => [
                'users' => $users,
                'educationals' => $educationals,
                'posts' => $posts,
                'ads' => $ads,
            ],
        ]);
    }

}
