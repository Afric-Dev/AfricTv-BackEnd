<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TmdbService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('tmdb.base_url');
        $this->apiKey = config('tmdb.api_key');
    }

    public function searchMovies($query)
    {
        $response = Http::get("{$this->baseUrl}/search/movie", [
            'api_key' => $this->apiKey,
            'query' => $query,
        ]);

        return $response->json();
    }

    public function getPopularMovies()
    {
        $response = Http::get("{$this->baseUrl}/movie/popular", [
            'api_key' => $this->apiKey,
        ]);

        return $response->json();
    }

    public function getMovieDetails($movieId)
    {
        $response = Http::get("{$this->baseUrl}/movie/{$movieId}", [
            'api_key' => $this->apiKey,
        ]);

        return $response->json();
    }
}
