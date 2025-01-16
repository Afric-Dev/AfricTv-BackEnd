<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ListenBrainzService
{
    private $apiBase = 'https://listenbrainz.org/search/';
    private $lastErrorMessage = '';

    /**
     * Search for artists using ListenBrainz API.
     *
     * @param string $query
     * @return array|null
     */
    public function searchArtist(string $query)
    {
        $endpoint = $this->apiBase;

        $response = Http::get($endpoint, [
            'search_term' => $query,
            'search_type' => 'artist', // Searching specifically for artists
        ]);

        // Log the full response body to check for errors
        \Log::info('ListenBrainz API Response:', [
            'status' => $response->status(),
            'body' => $response->body(), // Log the entire raw body
        ]);

        // If the request fails, capture and store the raw error message
        if ($response->failed()) {
            // Log the raw error message to help diagnose the issue
            $this->lastErrorMessage = $response->json()['error'] ?? $response->body() ?? 'Unknown error';

            \Log::error('Failed to fetch data from ListenBrainz.', [
                'status' => $response->status(),
                'error' => $this->lastErrorMessage,
            ]);

            // Return null if error is detected
            return null;
        }

        // Return the successful response if the API call is successful
        return $response->successful() ? $response->json() : null;
    }

    /**
     * Get the last error message from the ListenBrainz API.
     *
     * @return string
     */
    public function getLastErrorMessage()
    {
        return $this->lastErrorMessage;
    }
}
