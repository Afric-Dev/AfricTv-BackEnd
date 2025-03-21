<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class VidSrcService
{
    protected $client;
    protected $baseUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->baseUrl = 'https://player.vidsrc.co/embed/movie/';
    }

    public function getMovieEmbedUrlByTmdbId($tmdbId)
    {
        try {
            // VidSrc accepts both TMDb and IMDb IDs. Here, we use the TMDb ID directly.
            $embedUrl = "{$this->baseUrl}{$tmdbId}";
            return [
                'embed_url' => $embedUrl,
                'tmdb_id' => $tmdbId,
            ];
        } catch (\Exception $e) {
            Log::error('Error constructing movie embed URL: ' . $e->getMessage());
            return null;
        }
    }
}
