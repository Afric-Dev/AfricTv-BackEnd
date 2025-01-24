<?php

namespace App\Services;

use GuzzleHttp\Client;

class PopcornflixService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => env('POPCORNFLIX_BASE_URL'),
        ]);
    }

    public function getTopMovies()
    {
        $response = $this->client->get('/movies');
        return json_decode($response->getBody(), true);
    }

    public function searchMovies($query)
    {
        $response = $this->client->get('/search', [
            'query' => ['q' => $query],
        ]);
        return json_decode($response->getBody(), true);
    }
}
