<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class GeminiService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('GEMINI_API_KEY'); 
    }

    public function generateText($inputText)
    {
        $url = 'https://api.gemini.com/v1/generate';

        try {
            $response = $this->client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [ // Send the input text in JSON format
                    'prompt' => $inputText,
                    'max_tokens' => 100, // Adjust this based on your requirements
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            // Handle request errors
            throw new \Exception('Request failed: ' . $e->getMessage());
        }
    }
}
