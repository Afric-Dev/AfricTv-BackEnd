<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ListenBrainzService;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse; 

class MusicController extends Controller
{

    /**
     * Search for music on YouTube Music.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            return response()->json(['error' => 'Query parameter is required.'], 400);
        }

        // Mock YouTube Music search endpoint
        $url = 'https://www.googleapis.com/youtube/v3/search';

        // Your YouTube API Key
        $apiKey = env('YOUTUBE_API_KEY');

        // Perform the API request
        $response = Http::get($url, [
            'part' => 'snippet',
            'q' => $query,
            'type' => 'video',
            'videoCategoryId' => '10', // Music category
            'key' => $apiKey,
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to fetch data from YouTube.'], 500);
        }

        // Parse response data
        $data = $response->json();

        return response()->json([
            'results' => $data['items'] ?? [],
        ]);
    }

    /**
     * Search for artists by name and return their information.
     */
    // public function searchArtist(Request $request)
    // {
    //     $query = $request->input('query');
        
    //     // Validate the query
    //     if (empty($query)) {
    //         return response()->json([
    //             'error' => 'Query parameter is required.',
    //         ], 400);
    //     }

    //     // Step 1: Search for the artist(s) by name
    //     $artistResponse = Http::timeout(30)->get("https://musicbrainz.org/ws/2/artist", [
    //         'query' => $query,
    //         'fmt' => 'json',
    //     ]);

    //     if ($artistResponse->failed()) {
    //         return response()->json([
    //             'error' => 'Failed to fetch artist data from MusicBrainz.',
    //             'details' => $artistResponse->body(),  // Add response body for debugging
    //         ], $artistResponse->status());
    //     }

    //     $artistData = $artistResponse->json();

    //     // If no artists found
    //     if (empty($artistData['artists'])) {
    //         return response()->json([
    //             'error' => 'No artists found matching the name.',
    //         ], 404);
    //     }

    //     // Step 2: Return the list of artists with their MusicBrainz ID
    //     $artists = array_map(function ($artist) {
    //         // Only include available fields and avoid the 'type' key if it doesn't exist
    //         $type = $artist['type'] ?? 'Unknown'; // Default to 'Unknown' if 'type' is not set

    //         return [
    //             'id' => $artist['id'],
    //             'name' => $artist['name'],
    //             'type' => $type, // Use the 'type' field only if it exists
    //         ];
    //     }, $artistData['artists']);

    //     return response()->json([
    //         'query' => $query,
    //         'artists' => $artists,
    //     ]);
    // }

    // /**
    //  * Get the cover image for an artist.
    //  */
    // private function getArtistCoverImage($artistId)
    // {
    //     // Step 3: Fetch the artist's cover image using their MusicBrainz ID from the Cover Art Archive
    //     $coverImage = null;
    //     $coverResponse = Http::get("https://coverartarchive.org/artist/{$artistId}");

    //     if ($coverResponse->successful()) {
    //         $coverData = $coverResponse->json();
    //         if (!empty($coverData['images'])) {
    //             $coverImage = $coverData['images'][0]['thumbnails']['large'] ?? null;
    //         }
    //     }

    //     return $coverImage;
    // }
    // /**
    //  * Search for songs by artist ID.
    //  */
    //     /**
    //  * Get songs by Artist ID, including all raw data returned by MusicBrainz.
    //  */
    // public function searchSongsByArtistId($artistId)
    // {
    //     // Step 1: Search for recordings (songs) for the artist by MusicBrainz artist ID
    //     $songsResponse = Http::timeout(30)->get("https://musicbrainz.org/ws/2/recording", [
    //         'artist' => $artistId,  // Filter by artist ID
    //         'fmt' => 'json',
    //         'limit' => 100, // Limit for testing
    //     ]);

    //     if ($songsResponse->failed()) {
    //         return response()->json([
    //             'error' => 'Failed to fetch songs from MusicBrainz.',
    //             'details' => $songsResponse->body(),  // Add response body for debugging
    //         ], $songsResponse->status());
    //     }

    //     $songsData = $songsResponse->json();

    //     if (empty($songsData['recordings'])) {
    //         return response()->json([
    //             'error' => 'No songs found for this artist.',
    //         ], 404);
    //     }

    //     // Step 2: Extract song details and fetch cover image URLs
    //     $songs = array_map(function ($song) {
    //         // Extract basic song details
    //         $songDetails = [
    //             'title' => $song['title'],
    //             'id' => $song['id'],
    //             'first-release-date' => $song['first-release-date'] ?? null,
    //             'length' => $song['length'] ?? null,
    //             'video' => $song['video'] ?? false,
    //             'disambiguation' => $song['disambiguation'] ?? '',
    //             'url' => "https://listenbrainz.org/player/?recording_mbids={$song['id']}",
    //         ];

    //         // Fetch release-group details for the cover image
    //         $releaseGroup = $song['releases'][0]['release-group'] ?? null;
    //         if ($releaseGroup && isset($releaseGroup['id'])) {
    //             $coverImageUrl = "https://coverartarchive.org/release-group/{$releaseGroup['id']}/front";
    //             $songDetails['cover_image'] = $coverImageUrl;
    //         } else {
    //             $songDetails['cover_image'] = null; // No cover image available
    //         }

    //         return $songDetails;
    //     }, $songsData['recordings']);

    //     // Step 3: Return the enriched song data
    //     return response()->json([
    //         'songs' => $songs,
    //     ]);
    // }

    // public function searchMusicByTitle(Request $request)
    // {
    //     // Get the 'query' parameter from the request
    //     $query = $request->input('query');

    //     // Check if the 'query' parameter is missing or empty
    //     if (!$query || strlen($query) > 255) {
    //         return response()->json([
    //             'error' => 'The "query" parameter is required and must not exceed 255 characters.',
    //         ], 400);
    //     }

    //     // Step 1: Search for recordings by query
    //     $recordingsResponse = Http::timeout(30)->get("https://musicbrainz.org/ws/2/recording", [
    //         'query' => $query,  // Search for the music title
    //         'fmt' => 'json',
    //         'limit' => 10,  // Limit the results
    //     ]);

    //     if ($recordingsResponse->failed()) {
    //         return response()->json([
    //             'error' => 'Failed to fetch music recordings from MusicBrainz.',
    //             'details' => $recordingsResponse->body(),  // Include response body for debugging
    //         ], $recordingsResponse->status());
    //     }

    //     $recordingsData = $recordingsResponse->json();

    //     if (empty($recordingsData['recordings'])) {
    //         return response()->json(['error' => 'No recordings found for this query.'], 404);
    //     }

    //     // Step 2: Process recordings
    //     $songs = array_map(function ($recording) {
    //         return [
    //             'title' => $recording['title'],
    //             'id' => $recording['id'],
    //             'first-release-date' => $recording['first-release-date'] ?? null,
    //             'length' => $recording['length'] ?? null,
    //             'video' => $recording['video'] ?? false,
    //             'disambiguation' => $recording['disambiguation'] ?? '',
    //             'description' => $recording['disambiguation'] ?? 'No additional details available.', // Explicit descriptive text
    //             'url' => "https://listenbrainz.org/player/?recording_mbids={$recording['id']}",
    //         ];
    //     }, $recordingsData['recordings']);

    //     return response()->json(['songs' => $songs]);
    // }



   // public function search(Request $request): JsonResponse
    // {   

    //     $request->validate([
    //         'songTitle' => 'required|string',
    //     ]);

    //     $apiKey = '81fe63fb59msh9bfb7027d381b95p1beedcjsn85e0ef80df3b';
    //     $apiHost = 'spotify-downloader9.p.rapidapi.com';
    //     $albumId = $request->input('albumId', 'https://open.spotify.com/album/68GI09qAs2XLJmA3hj5K7y');
    //     $searchQuery = $request->input('songTitle'); // The song title you want to search for

    //     // Make API request to fetch album details
    //     $response = Http::withHeaders([
    //         'x-rapidapi-host' => $apiHost,
    //         'x-rapidapi-key'  => $apiKey,
    //     ])->get('https://spotify-downloader9.p.rapidapi.com/downloadAlbum', [
    //         'albumId' => $albumId,
    //     ]);

    //     if ($response->successful()) {
    //         // Get the response data
    //         $data = $response->json();

    //         // Filter the songs based on the search query
    //         $filteredSongs = collect($data['data']['songs'])->filter(function ($song) use ($searchQuery) {
    //             return stripos($song['title'], $searchQuery) !== false;
    //         })->values(); // Reindex the filtered array

    //         // If songs are found, return them, otherwise return a message
    //         if ($filteredSongs->isNotEmpty()) {
    //             return response()->json([
    //                 'success' => true,
    //                 'filteredSongs' => $filteredSongs,
    //             ]);
    //         } else {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'No songs found matching the search query.',
    //             ], 404);
    //         }
    //     } else {
    //         // Handle error
    //         return response()->json([
    //             'error' => 'Failed to fetch album details',
    //             'message' => $response->body(),
    //         ], $response->status());
    //     }
    // }


    // protected $shazamService;

    // public function __construct(ShazamService $shazamService)
    // {
    //     $this->shazamService = $shazamService;
    // }

    // // Search for a song by a query
    // public function search(Request $request)
    // {
    //     $request->validate([
    //         'search' => 'required|max:55',
    //     ]);

    //     $query = $request->input('search');
    //     $results = $this->shazamService->searchSong($query);

    //     // Check if there was an error
    //     if (isset($results['error'])) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => $results['error'],
    //             'data' => $results['message']
    //         ], 404); // 404 for client error
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Search Results for Songs',
    //         'data' => $results
    //     ]);
    // }

    // // Get song details by track id
    // public function getDetails($trackId)
    // {
    //     $songDetails = $this->shazamService->getSongDetails($trackId);

    //     // Check if there was an error
    //     if (isset($songDetails['error'])) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => $songDetails['error'],
    //             'data' => $songDetails['message']
    //         ], 404); // Return 404 for client error
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Song Details',
    //         'data' => $songDetails
    //     ]);
    // }
}
