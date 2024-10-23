<?php

namespace App\Services;

use GuzzleHttp\Client;

class BloomService
{
    protected $client;
    protected $huggingFaceToken;

    public function __construct()
    {
        $this->client = new Client();
        $this->huggingFaceToken = "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";
    }

    public function generateText($inputText)
    {
        $url = 'https://api-inference.huggingface.co/models/bigscience/bloom';

        try {
            $response = $this->client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->huggingFaceToken,
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode([
                    'inputs' => $inputText,
                    'options' => ['wait_for_model' => true],
                ]),
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Log error or return custom message
            throw new \Exception('Request failed: ' . $e->getMessage());
        }
    }

}
