<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class TMDbService
{
    protected $client;
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('TMDB_API_KEY');
        $this->baseUrl = 'https://api.themoviedb.org/3/';
    }

    public function searchMovies($query)
    {
        try {
            $response = $this->client->get("{$this->baseUrl}search/movie", [
                'query' => [
                    'api_key' => $this->apiKey,
                    'query' => $query,
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('Error searching movies in TMDb API: ' . $e->getMessage());
            return null;
        }
    }

    public function getMovieDetails($tmdbId)
    {
        try {
            // Fix the syntax error here
            $response = $this->client->get("{$this->baseUrl}movie/{$tmdbId}", [
                'query' => [
                    'api_key' => $this->apiKey,
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('Error fetching movie details from TMDb API: ' . $e->getMessage());
            return null;
        }
    }

    public function getTopMovies()
    {
        try {
            $response = $this->client->get("{$this->baseUrl}movie/top_rated", [
                'query' => [
                    'api_key' => $this->apiKey,
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('Error fetching top movies from TMDb API: ' . $e->getMessage());
            return null;
        }
    }
}
