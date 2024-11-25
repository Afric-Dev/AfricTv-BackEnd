<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use App\Models\Ads;
use App\Models\Educational;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;  

class TrendingController extends Controller
{


    public function trending(): JsonResponse
    {
        // Timeframe for trending (posts from the last 48 hours)
        $timeframe = Carbon::now()->subHours(48);

        // Fetch posts within the specified timeframe
        $posts = Post::where('created_at', '>=', $timeframe)->get();

        // Collection to store hashtag counts with category
        $hashtagCounts = collect();

        // Count occurrences of each hashtag and associate with category
        foreach ($posts as $post) {
            // Split hashtags by spaces or commas
            $trendings = preg_split('/[\s,]+/', $post->hashtags);

            // Extract the first word of the category
            $categoryFirstWord = strtok($post->category, ' '); // Get the first word of the category

            foreach ($trendings as $hashtag) {
                // Remove the # symbol and trim spaces
                $hashtag = ltrim(trim($hashtag), '#');

                if ($hashtag !== '') {
                    // Create the key with category and hashtag divided by ":"
                    $categoryAndHashtag = "{$categoryFirstWord} : {$hashtag}";
                    
                    // Increment the count of the hashtag
                    $hashtagCounts->put($categoryAndHashtag, $hashtagCounts->get($categoryAndHashtag, 0) + 1);
                }
            }
        }

        // Sort hashtags by their count in descending order
        $trendingHashtags = $hashtagCounts->sortDesc()->take(20);

        return response()->json([
            "status" => true,
            "message" => "Trending",
            "data" => $trendingHashtags,
        ]);
    }



public function search($searchQuery): JsonResponse
{
    // Remove any leading # or whitespace from the user's search query
    $searchQuery = ltrim($searchQuery, '# ');

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
        ->orWhere('category', 'LIKE', "%{$searchQuery}%")
        ->orWhereRaw("FIND_IN_SET(?, REPLACE(TRIM(LEADING ',' FROM hashtags), ' ', '')) > 0", [$searchQuery])
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
