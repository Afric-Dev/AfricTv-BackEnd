<?php

namespace App\Services;

use GuzzleHttp\Client;

class FalconService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('HUGGINGFACE_API_TOKEN'); 
    }

    public function generateText($inputText)
    {
        $url = 'https://api.example.com/v1/falcon/generate'; // Replace with the actual API endpoint for Falcon 180B

        try {
            $response = $this->client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'prompt' => $inputText,
                    'max_tokens' => 100, 
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Handle the error appropriately
            throw new \Exception('Request failed: ' . $e->getMessage());
        }
    }
}
