<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\justWatchService;
use Illuminate\Http\JsonResponse;

class MovieController extends Controller
{
    protected $justWatchService;

    public function __construct(JustWatchService $justWatchService)
    {
        $this->justWatchService = $justWatchService;
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $movies = $this->justWatchService->searchMovies($query);
        return response()->json($movies);
    }

    public function topMovies()
    {
        $movies = $this->justWatchService->getTopMovies();
        return response()->json($movies);
    }

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
