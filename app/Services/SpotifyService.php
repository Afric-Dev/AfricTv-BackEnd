<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SpotifyService
{
    protected $apiUrl = 'https://spotify23.p.rapidapi.com';

    protected function getHeaders()
    {
        return [
            'X-RapidAPI-Key' => env('RAPIDAPI_KEY'),
            'X-RapidAPI-Host' => 'spotify-web2.p.rapidapi.com'
        ];
    }

    // Search for music, artists, or albums
    public function search($query, $type = 'multi')
    {
        $response = Http::withHeaders($this->getHeaders())->get("{$this->apiUrl}/search/", [
            'q' => $query,
            'type' => $type
        ]);

        return $response->json();
    }

 // Get Top Songs (Using Spotify Global Chart Playlist)
    public function getTopSongs()
    {
        $playlist_id = "37i9dQZF1DXcBWIGoYBM5M"; // Spotify Global Top 50 Playlist ID
        $response = Http::withHeaders($this->getHeaders())->get("{$this->apiUrl}/playlist_tracks/", [
            'id' => $playlist_id
        ]);

        return $response->json();
    }

    // ✅ Get Top 5 Artists & Their Songs
    public function getTopArtists()
    {
        // Step 1: Get related artists using Rema's Spotify Artist ID
        $response = Http::withHeaders($this->getHeaders())->get("{$this->apiUrl}/artist_overview/", [
            'id' => '3wcj11K77LjEY1PkEazffa' // Rema's Spotify Artist ID
        ]);

        $data = $response->json();

        // Extract related artists (Limit to 5)
        $artists = array_slice($data['data']['artist']['relatedContent']['relatedArtists']['items'] ?? [], 0, 5);

        $formattedArtists = [];

        foreach ($artists as $artist) {
            $artistId = $artist['id'] ?? null;
            $artistName = $artist['profile']['name'] ?? 'Unknown';
            $artistImage = $artist['visuals']['avatarImage']['sources'][0]['url'] ?? null;
            $followers = $artist['stats']['followers'] ?? 0;
            $popularity = $artist['stats']['popularity'] ?? 0;
            $genres = $artist['genres'] ?? [];

            // Step 2: Get the artist's top 5 songs instead of albums
            $songsResponse = Http::withHeaders($this->getHeaders())->get("{$this->apiUrl}/artist_top_tracks/", [
                'id' => $artistId,
                'offset' => 0,
                'limit' => 5 // Only fetch 5 songs
            ]);

            $songsData = $songsResponse->json();
            $songs = array_slice($songsData['data']['artist']['discography']['topTracks']['items'] ?? [], 0, 5);

            $formattedSongs = [];
            foreach ($songs as $song) {
                $songId = $song['id'] ?? null;

                $formattedSongs[] = [
                    'title' => $song['name'] ?? 'Unknown Song',
                    'cover' => $song['album']['coverArt']['sources'][0]['url'] ?? null,
                    'url' => $songId ? "https://open.spotify.com/track/{$songId}" : null // Song playback URL
                ];
            }

            // Add artist and their songs to the final array
            $formattedArtists[] = [
                'name' => $artistName,
                'image' => $artistImage,
                'followers' => $followers,
                'popularity' => $popularity,
                'genres' => $genres,
                'songs' => $formattedSongs
            ];
        }

        return response()->json($formattedArtists);
    }

}
