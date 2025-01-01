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

        // Collection to store hashtag counts grouped by category
        $hashtagCounts = collect();

        foreach ($posts as $post) {
            // Split hashtags by spaces or commas
            $hashtags = preg_split('/[\s,]+/', $post->hashtags);

            // Extract the first word of the category
            $categoryFirstWord = strtok($post->category, ' ');

            foreach ($hashtags as $hashtag) {
                // Normalize the hashtag
                $normalizedHashtag = strtolower(ltrim(trim($hashtag), '#'));

                if ($normalizedHashtag !== '') {
                    // Create the key combining category and normalized hashtag
                    $categoryAndHashtag = "{$categoryFirstWord} : {$normalizedHashtag}";

                    // Increment the count of the hashtag
                    $hashtagCounts->put($categoryAndHashtag, $hashtagCounts->get($categoryAndHashtag, 0) + 1);
                }
            }
        }

        // Group by hashtags (ignoring category) and sum counts
        $aggregatedCounts = $hashtagCounts
            ->keys()
            ->groupBy(function ($key) {
                return explode(' : ', $key)[1]; // Group by hashtag only
            })
            ->map(function ($group) use ($hashtagCounts) {
                return $group->reduce(function ($carry, $key) use ($hashtagCounts) {
                    return $carry + $hashtagCounts[$key];
                }, 0);
            });

        // Sort aggregated hashtags by count in descending order
        $sortedHashtags = $aggregatedCounts->sortDesc()->take(20);

        // Format output with counts and categories
        $trendingHashtags = $sortedHashtags->map(function ($count, $hashtag) use ($hashtagCounts) {
            // Collect associated categories
            $categories = $hashtagCounts
                ->keys()
                ->filter(fn($key) => str_contains($key, " : {$hashtag}"))
                ->map(fn($key) => explode(' : ', $key)[0])
                ->unique();

            return [
                'hashtag' => $hashtag,
                'count' => $count,
                'categories' => $categories->values(),
            ];
        });

        return response()->json([
            "status" => true,
            "message" => "Trending",
            "data" => $trendingHashtags->values(),
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

        // Fetch Ads for Educational items (type 'VID')
        $adsEdu = Ads::where('ads_type', 'VID')->where('status', 'ACTIVE')->inRandomOrder()->limit(5)->get();

        // Inject ads into the educational posts list after every 3rd post
        $finalEducationals = collect();
        $educationals->values()->each(function ($edu, $index) use ($adsEdu, &$finalEducationals) {
            $finalEducationals->push($edu);
            // Add an ad after every 3rd post
            if (($index + 1) % 3 === 0 && $adsEdu->isNotEmpty()) {
                $finalEducationals->push($adsEdu->shift()); // Take the next ad
            }
        });

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

        // Fetch Ads for Posts (type 'PIC' or 'LINK')
        $adsPost = Ads::where(function ($query) {
            $query->where('ads_type', 'PIC')
                  ->orWhere('ads_type', 'LINK');
        })->where('status', 'ACTIVE')->inRandomOrder()->limit(5)->get();

        // Inject ads into the post list after every 5th post
        $finalPosts = collect();
        $posts->values()->each(function ($post, $index) use ($adsPost, &$finalPosts) {
            $finalPosts->push($post);
            // Add an ad after every 5th post
            if (($index + 1) % 5 === 0 && $adsPost->isNotEmpty()) {
                $finalPosts->push($adsPost->shift()); // Take the next ad
            }
        });

        // Search ads Posts (if needed)
        $ads = Ads::where('title', 'LIKE', "%{$searchQuery}%")
            ->orWhere('description', 'LIKE', "%{$searchQuery}%")
            ->get();

        return response()->json([
            "status" => true,
            "message" => "Search results",
            "data" => [
                'users' => $users,
                'educationals' => $finalEducationals,
                'posts' => $finalPosts,
                'ads' => $ads,
            ],
        ]);
    }


}
