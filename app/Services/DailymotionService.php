<?php

namespace App\Services;

use GuzzleHttp\Client;

class DailymotionService
{
    protected $client;
    protected $baseUrl = 'https://api.dailymotion.com';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    // Get movies
    public function getMovies($limit = 20)
    {
        $response = $this->client->get($this->baseUrl . "/videos", [
            'query' => [
                'limit' => $limit,
            ]
        ]);
        $videos = json_decode($response->getBody()->getContents(), true);

        // Add the URL to watch each movie
        foreach ($videos['list'] as &$video) {
            $video['watch_url'] = 'https://www.dailymotion.com/video/' . $video['id'];
        }

        return $videos;
    }

    public function searchMovies($query, $limit = 25)
    {
        $response = $this->client->get($this->baseUrl . "/videos", [
            'query' => [
                'limit' => $limit,
                'search' => $query,  // search query
            ]
        ]);
        $movies = json_decode($response->getBody()->getContents(), true);

        // Add the URL to watch each movie
        foreach ($movies['list'] as &$movie) {
            $movie['watch_url'] = 'https://www.dailymotion.com/video/' . $movie['id'];
        }

        return $movies;
    }

}
