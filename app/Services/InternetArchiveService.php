<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class InternetArchiveService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'https://archive.org/advancedsearch.php';
    }

    /**
     * Search for movies in the Internet Archive.
     *
     * @param string $query
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function searchMovies($query, $page = 1, $limit = 10)
    {
        $response = Http::get($this->baseUrl, [
            'q' => "{$query} AND mediatype:movies",
            'fl' => 'title,creator,year,identifier',
            'page' => $page,
            'rows' => $limit,
            'output' => 'json',
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        \Log::error('Internet Archive API Error', [
            'response' => $response->body(),
        ]);

        return ['error' => 'Failed to fetch data from Internet Archive'];
    }

}