<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ReplicateService
{
    protected $baseUri;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUri = config('services.replicate.base_uri');
        $this->apiKey = config('services.replicate.key');
    }

    public function makePrediction($model, $input)
    {
        $url = $this->baseUri . "predictions";

        $response = Http::withHeaders([
            'Authorization' => 'Token ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($url, [
            'version' => $model,
            'input' => $input,
        ]);

        return $response->json();
    }

    public function getPredictionStatus($predictionId)
    {
        $url = $this->baseUri . "predictions/{$predictionId}";

        $response = Http::withHeaders([
            'Authorization' => 'Token ' . $this->apiKey,
        ])->get($url);

        return $response->json();
    }
}
