<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ShazamService;
use Illuminate\Support\Facades\Http;

class MusicController extends Controller
{
    public function search(Request $request)
    {   

        $request->validate([
            'songTitle' => 'required|string',
        ]);

        $apiKey = '81fe63fb59msh9bfb7027d381b95p1beedcjsn85e0ef80df3b';
        $apiHost = 'spotify-downloader9.p.rapidapi.com';
        $albumId = $request->input('albumId', 'https://open.spotify.com/album/68GI09qAs2XLJmA3hj5K7y');
        $searchQuery = $request->input('songTitle'); // The song title you want to search for

        // Make API request to fetch album details
        $response = Http::withHeaders([
            'x-rapidapi-host' => $apiHost,
            'x-rapidapi-key'  => $apiKey,
        ])->get('https://spotify-downloader9.p.rapidapi.com/downloadAlbum', [
            'albumId' => $albumId,
        ]);

        if ($response->successful()) {
            // Get the response data
            $data = $response->json();

            // Filter the songs based on the search query
            $filteredSongs = collect($data['data']['songs'])->filter(function ($song) use ($searchQuery) {
                return stripos($song['title'], $searchQuery) !== false;
            })->values(); // Reindex the filtered array

            // If songs are found, return them, otherwise return a message
            if ($filteredSongs->isNotEmpty()) {
                return response()->json([
                    'success' => true,
                    'filteredSongs' => $filteredSongs,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No songs found matching the search query.',
                ], 404);
            }
        } else {
            // Handle error
            return response()->json([
                'error' => 'Failed to fetch album details',
                'message' => $response->body(),
            ], $response->status());
        }
    }


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
