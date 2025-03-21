<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class PlexService
{
    protected $baseUrl;
    protected $token;
    protected $libraryId;

    public function __construct()
    {
        $this->baseUrl = 'http://' . env('PLEX_IP_ADDRESS') . ':32400';
        $this->token = env('PLEX_TOKEN');
        $this->libraryId = env('PLEX_MOVIE_LIBRARY_ID');
    }

    /**
     * Retrieve all movies from the Plex server.
     *
     * @return array
     * @throws \Exception
     */
    public function getMovies()
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->get("{$this->baseUrl}/library/sections/{$this->libraryId}/all", [
            'X-Plex-Token' => $this->token,
        ]);

        if ($response->failed()) {
            $this->handleError($response);
        }

        return $response->json();
    }

    /**
     * Search for movies by title.
     *
     * @param string $query
     * @return array
     * @throws \Exception
     */
    public function searchMovies(string $query)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->get("{$this->baseUrl}/library/sections/{$this->libraryId}/all", [
            'X-Plex-Token' => $this->token,
            'type' => 'movie',
            'title' => $query,
        ]);

        if ($response->failed()) {
            $this->handleError($response);
        }

        return $response->json();
    }

    /**
     * Handle HTTP errors and throw exceptions with detailed messages.
     *
     * @param \Illuminate\Http\Client\Response $response
     * @throws \Exception
     */
    protected function handleError($response)
    {
        $status = $response->status();
        $body = $response->body();

        switch ($status) {
            case 400:
                $message = 'Bad Request';
                break;
            case 401:
                $message = 'Unauthorized - Invalid Plex Token';
                break;
            case 404:
                $message = 'Not Found';
                break;
            case 500:
                $message = 'Internal Server Error';
                break;
            default:
                $message = 'HTTP Error';
        }

        throw new \Exception("{$message}: {$body}", $status);
    }
}
