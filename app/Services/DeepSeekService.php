<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class DeepSeekService
{
    protected $client;
    protected $apiKey;
    protected $baseUri;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('DEEPSEEK_API_KEY'); // Load API key from .env
        $this->baseUri = 'https://api.deepseek.com/'; // Base URI for the API
    }

    /**
     * Make a request to the DeepSeek API.
     *
     * @param string $endpoint
     * @param string $method
     * @param array $data
     * @return array
     */
    public function makeRequest($endpoint, $method = 'POST', $data = [])
    {
        try {
            $response = $this->client->request($method, $this->baseUri . $endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => $data, // Send data as JSON
            ]);

            // Decode the JSON response
            return json_decode($response->getBody(), true);
        } catch (GuzzleException $e) {
            // Log the error
            Log::error('DeepSeek API Error: ' . $e->getMessage(), [
                'endpoint' => $this->baseUri . $endpoint,
                'method' => $method,
                'data' => $data,
                'exception' => $e,
            ]);
            return ['error' => $e->getMessage()];
        }
    }
}