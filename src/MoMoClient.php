<?php
namespace Angstrom\MoMo;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class MoMoClient
{
    protected $client;
    protected $apiKey;
    protected $apiUser;
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = config('momo.api_key');
        $this->apiUser = config('momo.api_user');
        $this->apiBaseUrl = config('momo.api_base_url');
    }

    public function getToken()
    {
        if (Cache::has('momo_token')) {
            return Cache::get('momo_token');
        }

        $response = $this->client->post("{$this->apiBaseUrl}/v1_0/apiuser/{$this->apiUser}/apikey", [
            'headers' => [
                'Ocp-Apim-Subscription-Key' => $this->apiKey,
            ],
        ]);

        $token = json_decode($response->getBody()->getContents(), true)['access_token'];

        Cache::put('momo_token', $token, 3600);

        return $token;
    }

    public function requestPayment($amount, $currency, $externalId, $payer, $payerMessage, $payeeNote)
    {
        $token = $this->getToken();

        $response = $this->client->post("{$this->apiBaseUrl}/v1_0/transfer", [
            'headers' => [
                'Authorization' => "Bearer {$token}",
                'Ocp-Apim-Subscription-Key' => $this->apiKey,
                'X-Reference-Id' => $externalId,
                'X-Target-Environment' => 'sandbox',
            ],
            'json' => [
                'amount' => $amount,
                'currency' => $currency,
                'externalId' => $externalId,
                'payer' => $payer,
                'payerMessage' => $payerMessage,
                'payeeNote' => $payeeNote,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
