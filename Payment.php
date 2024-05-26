<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Payment
{
    private $merchantToken = 'Basic <TOKEN>'; 
    private $username = '<USERNAME>';  // Merchant username
    private $password = '<PASSWORD>';  // Merchant password
    private $grantType = 'password';
    private $transactionPin = '<TRANSACTION_PIN>'; // Merchant MPIN
    private $orderId = '263626';  // Provided by the merchant
    private $amount = '10';  // Provided by the merchant
    private $languageCode = 'en';
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://uatgw1.nasswallet.com/payment/transaction/',
            'timeout' => 10.0
        ]);
    }

    /**
     * Fetches a merchant token using merchant account credentials.
     * @return string|null The merchant token or an error message.
     */
    public function loginWithMerchantAccount()
    {
        $payload = [
            'data' => [
                'username' => $this->username,
                'password' => $this->password,
                'grantType' => $this->grantType
            ]
        ];

        try {
            $response = $this->client->request('POST', 'login', [
                'headers' => ['authorization' => $this->merchantToken],
                'json' => $payload
            ]);

            $responseData = json_decode($response->getBody());

            if ($responseData->responseCode == 0 && $responseData->data->access_token) {
                return $this->makeTransaction($responseData->data->access_token);
            } else {
                return $responseData->message ?? 'Login failed.';
            }
        } catch (RequestException $e) {
            return 'Error fetching merchant token: ' . $e->getMessage();
        }
    }

    /**
     * Initiates a payment transaction using the provided access token.
     * @param string $accessToken The access token for authorization.
     * @return string|null The transaction URL or an error message.
     */
    public function makeTransaction($accessToken)
    {
        $payload = [
            'data' => [
                'userIdentifier' => $this->username,
                'transactionPin' => $this->transactionPin,
                'orderId' => $this->orderId,
                'amount' => $this->amount,
                'languageCode' => $this->languageCode
            ]
        ];

        try {
            $response = $this->client->request('POST', 'initTransaction', [
                'headers' => ['authorization' => 'Bearer ' . $accessToken],
                'json' => $payload
            ]);

            $responseData = json_decode($response->getBody());

            if ($responseData->responseCode == 0 && $responseData->data->transactionId) {
                return "https://uatcheckout1.nasswallet.com/payment-gateway?transactionId={$responseData->data->transactionId}&token={$responseData->data->token}&userIdentifier={$this->username}";
            } else {
                return 'Transaction failed.';
            }
        } catch (RequestException $e) {
            return 'Error initiating payment: ' . $e->getMessage();
        }
    }
}

// Usage example
$payment = new Payment();
$result = $payment->loginWithMerchantAccount();
echo $result;
