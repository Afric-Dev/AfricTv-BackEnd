<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\DailymotionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use App\Services\TMDbService;
use App\Services\VidSrcService;


class MovieController extends Controller
{

    protected $tmdbService;
    protected $vidSrcService;

    public function __construct(TMDbService $tmdbService, VidSrcService $vidSrcService)
    {
        $this->tmdbService = $tmdbService;
        $this->vidSrcService = $vidSrcService;
    }

    public function search(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
        ]);

        $title = $request->input('title');
        $searchResults = $this->tmdbService->searchMovies($title);

        if (empty($searchResults['results'])) {
            return response()->json(['error' => 'Movie not found.'], 404);
        }

        $movie = $searchResults['results'][0];
        $movieDetails = $this->tmdbService->getMovieDetails($movie['id']);
        $vidSrcData = $this->vidSrcService->getMovieEmbedUrlByTmdbId($movie['id']);

        if ($vidSrcData) {
            $movieDetails['embed_url'] = $vidSrcData['embed_url'];
            $movieDetails['vidsrc'] = $vidSrcData;
        } else {
            $movieDetails['embed_url'] = 'Streaming link not available.';
        }

        return response()->json($movieDetails);
    }

    public function topMovies()
    {
        $topMovies = $this->tmdbService->getTopMovies();

        if (empty($topMovies['results'])) {
            return response()->json(['error' => 'No top movies found.'], 404);
        }

        $moviesWithEmbedUrls = array_map(function ($movie) {
            $movieDetails = $this->tmdbService->getMovieDetails($movie['id']);
            $vidSrcData = $this->vidSrcService->getMovieEmbedUrlByTmdbId($movie['id']);
            $movieDetails['embed_url'] = $vidSrcData['embed_url'] ?? 'Streaming link not available.';
            $movieDetails['vidsrc'] = $vidSrcData;
            return $movieDetails;
        }, $topMovies['results']);

        return response()->json($moviesWithEmbedUrls);
    }
     // Get Top Movie Videos
    public function getTopMovieVideos()
    {
        // YouTube API URL for fetching most popular videos
        $url = 'https://www.googleapis.com/youtube/v3/videos';

        // Your YouTube API Key
        $apiKey = env('YOUTUBE_API_KEY');

        // Perform the API request
        $response = Http::get($url, [
            'part' => 'snippet,contentDetails',
            'chart' => 'mostPopular',
            'videoCategoryId' => '1', // Movie category (1 for Movies)
            'key' => $apiKey,
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to fetch data from YouTube.'], 500);
        }

        // Parse response data
        $data = $response->json();

        return response()->json([
            'results' => $data['items'] ?? [],
        ]);
    }

    // Search Movie Videos
    public function searchMovies(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            return response()->json(['error' => 'Query parameter is required.'], 400);
        }

        // YouTube API URL for searching videos
        $url = 'https://www.googleapis.com/youtube/v3/search';

        // Your YouTube API Key
        $apiKey = env('YOUTUBE_API_KEY');

        // Perform the API request
        $response = Http::get($url, [
            'part' => 'snippet',
            'q' => $query,
            'type' => 'video',
            'videoCategoryId' => '1', // Movie category (1 for Movies)
            'key' => $apiKey,
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to fetch data from YouTube.'], 500);
        }

        // Parse response data
        $data = $response->json();

        return response()->json([
            'results' => $data['items'] ?? [],
        ]);
    }

    // Get Single Movie Video Details
    public function getSingleMovieVideo($id)
    {
        // YouTube API URL for fetching video details
        $url = 'https://www.googleapis.com/youtube/v3/videos';

        // Your YouTube API Key
        $apiKey = env('YOUTUBE_API_KEY');

        // Perform the API request
        $response = Http::get($url, [
            'part' => 'snippet,contentDetails',
            'id' => $id,
            'key' => $apiKey,
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to fetch data from YouTube.'], 500);
        }

        // Parse response data
        $data = $response->json();

        return response()->json([
            'video' => $data['items'][0] ?? [],
        ]);
    }

    // protected $dailymotion;

    // public function __construct(DailymotionService $dailymotion)
    // {
    //     $this->dailymotion = $dailymotion;
    // }

    // // Fetch movies
    // public function getMovies()
    // {
    //     $movies = $this->dailymotion->getMovies();
    //     return response()->json($movies);
    // }

    // // Search for movies
    // public function searchMovies(Request $request)
    // {
    //     $query = $request->input('query');
    //     $movies = $this->dailymotion->searchMovies($query);
    //     return response()->json($movies);
    // }

    // public function getSingleVideo($videoId)
    // {
    //     $video = $this->dailymotion->getSingleVideo($videoId);
    //     return response()->json($video);
    // }

    // protected $tmdbService;

    // public function __construct(TmdbService $tmdbService)
    // {
    //     $this->tmdbService = $tmdbService;
    // }

    // // Search movies and return JSON
    // public function search($query): JsonResponse
    // {
    //     $movies = $this->tmdbService->searchMovies($query);

    //     return response()->json([
    //         'success' => true,
    //         'data' => $movies['results'],
    //     ]);
    // }

    // // Get popular movies and return JSON
    // public function popular(): JsonResponse
    // {
    //     $movies = $this->tmdbService->getPopularMovies();

    //     return response()->json([
    //         'success' => true,
    //         'data' => $movies['results'],
    //     ]);
    // }

    // // Get movie details and return JSON
    // public function details($movieId): JsonResponse
    // {
    //     $movie = $this->tmdbService->getMovieDetails($movieId);

    //     return response()->json([
    //         'success' => true,
    //         'data' => $movie,
    //     ]);
    // }
}
