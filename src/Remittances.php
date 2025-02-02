<?php

namespace Angstrom\MoMo;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class Remittances extends MoMoClient
{
    /**
     * Transfer money internationally
     */
    public function transfer($amount, $currency, $externalId, $payeeId, $payeeIdType = 'MSISDN', $payerMessage = '', $payeeNote = '')
    {
        $token = $this->getToken();
        $referenceId = $this->generateUUID();

        $response = $this->client->post("{$this->apiBaseUrl}/remittance/v1_0/transfer", [
            'headers' => [
                'Authorization' => "Bearer {$token}",
                'X-Reference-Id' => $referenceId,
                'X-Target-Environment' => config('momo.environment', 'sandbox'),
                'Ocp-Apim-Subscription-Key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'amount' => $amount,
                'currency' => $currency,
                'externalId' => $externalId,
                'payee' => [
                    'partyIdType' => $payeeIdType,
                    'partyId' => $payeeId
                ],
                'payerMessage' => $payerMessage,
                'payeeNote' => $payeeNote
            ]
        ]);

        return [
            'status' => $response->getStatusCode(),
            'referenceId' => $referenceId
        ];
    }

    /**
     * Get transfer status
     */
    public function getTransferStatus($referenceId)
    {
        $token = $this->getToken();

        $response = $this->client->get("{$this->apiBaseUrl}/remittance/v1_0/transfer/{$referenceId}", [
            'headers' => [
                'Authorization' => "Bearer {$token}",
                'X-Target-Environment' => config('momo.environment', 'sandbox'),
                'Ocp-Apim-Subscription-Key' => $this->apiKey
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Get account balance
     */
    public function getAccountBalance()
    {
        $token = $this->getToken();

        $response = $this->client->get("{$this->apiBaseUrl}/remittance/v1_0/account/balance", [
            'headers' => [
                'Authorization' => "Bearer {$token}",
                'X-Target-Environment' => config('momo.environment', 'sandbox'),
                'Ocp-Apim-Subscription-Key' => $this->apiKey
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Check account holder status
     */
    public function checkAccountHolderStatus($accountHolderId, $accountHolderIdType = 'MSISDN')
    {
        $token = $this->getToken();

        $response = $this->client->get("{$this->apiBaseUrl}/remittance/v1_0/accountholder/{$accountHolderIdType}/{$accountHolderId}/active", [
            'headers' => [
                'Authorization' => "Bearer {$token}",
                'X-Target-Environment' => config('momo.environment', 'sandbox'),
                'Ocp-Apim-Subscription-Key' => $this->apiKey
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Get exchange rate
     */
    public function getExchangeRate($sourceCurrency, $targetCurrency)
    {
        $token = $this->getToken();

        $response = $this->client->get("{$this->apiBaseUrl}/remittance/v1_0/exchangerate", [
            'headers' => [
                'Authorization' => "Bearer {$token}",
                'X-Target-Environment' => config('momo.environment', 'sandbox'),
                'Ocp-Apim-Subscription-Key' => $this->apiKey
            ],
            'query' => [
                'sourceCurrency' => $sourceCurrency,
                'targetCurrency' => $targetCurrency
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Generate UUID v4
     */
    private function generateUUID()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
