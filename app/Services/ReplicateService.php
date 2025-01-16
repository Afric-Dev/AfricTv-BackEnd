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

    public function makePrediction($model, $input, $version = null)
    {
        $url = $this->baseUri . "predictions";

        $payload = [
            'version' => $version ?? 'f1d50bb24186c52daae319ca8366e53debdaa9e0ae7ff976e918df752732ccc4',
            'input' => $input, 
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Token ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($url, $payload);

        return $response->json();
    }

    public function getPredictionStatus($predictionId)
    {
        $url = $this->baseUri . "predictions/{$predictionId}";

        $response = Http::withHeaders([
            'Authorization' => 'Token ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->get($url);

        return $response->json();
    }
}
