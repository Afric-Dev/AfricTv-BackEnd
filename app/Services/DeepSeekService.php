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
        $this->baseUri = 'https://api.deepseek.com/v1/'; // Replace with the actual DeepSeek API base URL
    }

    /**
     * Make a request to the DeepSeek API.
     *
     * @param string $endpoint
     * @param string $method
     * @param array $data
     * @return array
     */
    public function makeRequest($endpoint, $method = 'GET', $data = [])
    {
        try {
            $response = $this->client->request($method, $this->baseUri . $endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => $data,
            ]);

            return json_decode($response->getBody(), true);
        } catch (GuzzleException $e) {
            // Log the error
            Log::error('DeepSeek API Error: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}