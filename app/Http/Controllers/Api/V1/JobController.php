<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class JobController extends Controller
{
    private $apiHost;
    private $apiKey;

    public function __construct()
    {
        $this->apiHost = config('services.rapidapi.host_job');
        $this->apiKey = config('services.rapidapi.key');
    }

    /**
     * Fetch top jobs
     */
    public function getTopJobs()
    {
        $response = Http::withHeaders([
            'X-RapidAPI-Host' => $this->apiHost,
            'X-RapidAPI-Key' => $this->apiKey,
        ])->get("https://{$this->apiHost}/search", [
            'query' => 'top jobs', 
            'num_pages' => 1
        ]);

        return response()->json($response->json(), $response->status());
    }

    /**
     * Search for jobs based on a keyword
     */
    public function searchJobs(Request $request)
    {
        $query = $request->query('q', 'jobs'); // Default search: 'jobs'

        $response = Http::withHeaders([
            'X-RapidAPI-Host' => $this->apiHost,
            'X-RapidAPI-Key' => $this->apiKey,
        ])->get("https://{$this->apiHost}/search", [
            'query' => $query,
            'num_pages' => 1
        ]);

        return response()->json($response->json(), $response->status());
    }
        public function getJobDetails($id)
    {
        $response = Http::withHeaders([
            'X-RapidAPI-Host' => $this->apiHost,
            'X-RapidAPI-Key' => $this->apiKey,
        ])->get("https://{$this->apiHost}/job-details", [
            'job_id' => $id,
        ]);

        if ($response->successful()) {
            return response()->json($response->json(), 200);
        } else {
            return response()->json(['error' => 'Unable to fetch job details'], $response->status());
        }
    }
}
