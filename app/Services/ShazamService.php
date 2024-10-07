<?php 

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ShazamService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('services.shazam.base_uri'),
            'headers' => [
                'X-RapidAPI-Key' => config('services.shazam.key'),
                'X-RapidAPI-Host' => 'shazam.p.rapidapi.com',
            ],
            'timeout' => 5.0,
        ]);
    }

    // Function to search for a song
    public function searchSong($query)
    {
        try {
            // Correct '/search' endpoint for songs
            $response = $this->client->get('artists/get-latest-release', [
                'query' => [
                    'term' => $query,  // The search term you are looking for (song, artist, etc.)
                    'locale' => 'en-US',
                    'offset' => '0',
                    'limit' => '5', 
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            return [
                'error' => 'Failed to fetch search results',
                'message' => $e->getMessage(),
            ];
        }
    }


    // Function to get song details by track id
    public function getSongDetails($trackId)
    {
        try {
            $response = $this->client->get('songs/get-details', [
                'query' => [
                    'key' => $trackId,
                    'locale' => 'en-US',
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            return [
                'error' => 'Failed to fetch song details',
                'message' => $e->getMessage(),
            ];
        }
    }
}
