<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class JustWatchService
{
    protected $apiUrl;
    protected $partnerToken;

    public function __construct()
    {
        $this->apiUrl = config('services.justwatch.api_url');
        $this->partnerToken = config('services.justwatch.partner_token');
    }

    public function getPopularMovies()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->partnerToken,
        ])->get("{$this->apiUrl}/content/titles/popular", [
            'body' => '{}',
        ]);

        if ($response->successful()) {
            return $response->json()['items'];
        }

        return [];
    }

    public function searchMovies($query)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->partnerToken,
        ])->get("{$this->apiUrl}/content/titles", [
            'q' => $query,
        ]);

        if ($response->successful()) {
            return $response->json()['items'];
        }

        return [];
    }
}
