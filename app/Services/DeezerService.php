 <?php 

 namespace App\Services;

use GuzzleHttp\Client;

class DeezerService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('services.deezer.base_uri'), // Use config for the API base URL
            'timeout'  => 5.0,
        ]);
    }

    public function search($query)
    {
        $response = $this->client->get('search', [
            'query' => [
                'q' => $query
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getArtist($id)
    {
        $response = $this->client->get("artist/{$id}");

        return json_decode($response->getBody()->getContents(), true);
    }

    // Add more API functions as needed
}
