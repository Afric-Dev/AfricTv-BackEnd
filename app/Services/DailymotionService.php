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
public function getMovies($limit = 5)
{
    $response = $this->client->get($this->baseUrl . "/videos", [
        'query' => [
            'limit' => $limit,
        ]
    ]);
    $videos = json_decode($response->getBody()->getContents(), true);

    // Add the URL, thumbnail, and description for each movie
    foreach ($videos['list'] as &$video) {
        $video['watch_url'] = 'https://www.dailymotion.com/video/' . $video['id'];
        $video['thumbnail_url'] = $video['thumbnail_url'] ?? ''; // Thumbnail URL
        $video['description'] = $video['description'] ?? ''; // description
    }

    return $videos;
}

public function searchMovies($query, $limit = 5)
{
    $response = $this->client->get($this->baseUrl . "/videos", [
        'query' => [
            'limit' => $limit,
            'search' => $query,  // search query
        ]
    ]);
    $movies = json_decode($response->getBody()->getContents(), true);

    // Add the URL, thumbnail, and description for each search result
    foreach ($movies['list'] as &$movie) {
        $movie['watch_url'] = 'https://www.dailymotion.com/video/' . $movie['id'];
        $movie['thumbnail_url'] = $movie['thumbnail_url'] ?? ''; // Thumbnail URL
        $movie['description'] = $movie['description'] ?? ''; // Description
    }

    return $movies;
}

public function getSingleVideo($videoId)
{
    try {
        $response = $this->client->get("https://api.dailymotion.com/video/{$videoId}");
        $video = json_decode($response->getBody()->getContents(), true);

        return [
            'title' => $video['title'] ?? 'No title available',
            'watch_url' => 'https://www.dailymotion.com/video/' . $video['id'],
            'description' => $video['description'] ?? 'No description available',
            'thumbnail_url' => $video['thumbnail_url'] ?? 'No thumbnail available',
            'created_at' => $video['created_at'] ?? 'No date available',
            'duration' => $video['duration'] ?? 'No duration available',
            'views' => $video['views_total'] ?? 'No view count available',
        ];
    } catch (\GuzzleHttp\Exception\RequestException $e) {
        return response()->json(['error' => 'Failed to fetch video', 'message' => $e->getMessage()], 500);
    }
}


}
