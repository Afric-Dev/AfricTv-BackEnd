<?php

namespace App\Services;

use GuzzleHttp\Client;

class SpotifyService
{
    protected $client;
    protected $apiKey;
    protected $baseUri;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('RAPIDAPI_KEY'); 
        $this->baseUri = 'https://spotify-downloader9.p.rapidapi.com'; 
    }

    // Search for a song by its name
    public function searchSong($query)
    {
        $response = $this->client->request('GET', $this->baseUri . '/search', [
            'query' => [
                'term' => $query,
                'limit' => 5  
            ],
            'headers' => [
                'X-RapidAPI-Key' => $this->apiKey,
                'X-RapidAPI-Host' => 'spotify-downloader9.p.rapidapi.com'
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        // If data is successful, return relevant parts
        if (isset($data['success']) && $data['success']) {
            return $this->formatSongData($data['data']);
        }

        // Handle any failure
        return ['error' => 'No data found'];
    }

    // Get song details by track id
    public function getSongDetails($trackId)
    {
        $response = $this->client->request('GET', $this->baseUri . '/getDetails', [
            'query' => [
                'trackId' => $trackId
            ],
            'headers' => [
                'X-RapidAPI-Key' => $this->apiKey,
                'X-RapidAPI-Host' => 'spotify-downloader9.p.rapidapi.com'
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        if (isset($data['success']) && $data['success']) {
            return $this->formatSongData($data['data']);
        }

        return ['error' => 'No details found for this track'];
    }

    // Format the data to only return relevant parts
    private function formatSongData($data)
    {
        // Album Details
        $albumDetails = $data['albumDetails'] ?? null;

        // Songs List
        $songs = $data['songs'] ?? [];

        // Prepare formatted data
        $formattedData = [
            'album' => [
                'artist' => $albumDetails['artist'] ?? '',
                'title' => $albumDetails['title'] ?? '',
                'cover' => $albumDetails['cover'] ?? '',
                'releaseDate' => $albumDetails['releaseDate'] ?? ''
            ],
            'songs' => []
        ];

        // Loop through each song and extract relevant information
        foreach ($songs as $song) {
            $formattedData['songs'][] = [
                'id' => $song['id'],
                'artist' => $song['artist'],
                'title' => $song['title'],
                'album' => $song['album'],
                'cover' => $song['cover'],
                'releaseDate' => $song['releaseDate'],
                'downloadLink' => $song['downloadLink']  // Include download link
            ];
        }

        return $formattedData;
    }
}
