<?php
declare(strict_types=1);
namespace App\Service\Client;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ExchangeRateApiService
{
    private HttpClientInterface $client;
    private string $apiUrl;
    private string $accessKey;
    public function __construct(HttpClientInterface $client, string $apiUrl, string $accessKey)
    {
        $this->client = $client;
        $this->apiUrl = $apiUrl;
        $this->accessKey = $accessKey;
    }

    public function getExchangeRates(): array
    {
        try {
            $response = $this->client->request('GET', $this->apiUrl, [
                'query' => [
                    'access_key' => $this->accessKey
                ]
            ]);

            $data = $response->toArray();

            return $data['rates'];
        } catch (\Exception $e) {
            throw new \Exception('Error fetching exchange rates: ' . $e->getMessage());
        }
    }
}
