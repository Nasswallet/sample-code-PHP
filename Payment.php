<?php

use GuzzleHttp\Client;
use Guzzle\Http\EntityBody;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;
use GuzzleHttp\Exception\RequestException;


class Payment

{

    private $merchantToken = "Basic TUVSQ0hBTlRfQVBQOk1lcmNoYW50QEFkbWluIzEyMw=="; 
    private $username = "";  // merchant user ID will be provided by Nasswallet
    private $password = "";    // merchant password 
    private $grantType = "password";
    private $transactionPin = "";   //merchant's MPIN.
    private $orderId = "263626";     //will be provided by the merchant
    private $amount = "10";          //will be provided by the merchant
    private $languageCode = "en";

    private $client;


    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://uatgw.nasswallet.com/payment/transaction/',
            'timeout' => '1000'
        ]);
    }

    public function makePayment()
    {

        $response = $this->getMerchantToken($this->client, $this->merchantToken);

        $payload = [
            'data' => [
                'userIdentifier' => $this->username,
                'transactionPin' => $this->transactionPin,
                'orderId' => $this->orderId,
                'amount' => $this->amount,
                'languageCode' => $this->languageCode
            ]
        ];

        $this->payWithNasswallet($response, $payload);
        
    }


    public function getMerchantToken($client, $merchantToken)
    {     //generates and returns a unique access token.
        $payload = [
            "data" => [
                "username" => $this->username,
                "password" => $this->password,
                "grantType" => $this->grantType
            ]
        ];

        $response = json_decode($client->request('POST', 'login', [
            "headers" => ['authorization' => "$merchantToken"],
            'json' => $payload

        ])->getBody());


        if ($response->responseCode == 0 && $response->data->access_token) {
            return $response->data->access_token;
        } else {
            return 0;
        }
    }


    public function payWithNasswallet($access_token, $payload)
    {

        $response = json_decode($this->client->request('POST', 'initTransaction', [
            "headers" => ['authorization' => "Bearer $access_token"],
            "json" => $payload
        ])->getBody());

        if ($response->responseCode == 0 && $response->data->transactionId) {

            echo "https://uatcheckout.nasswallet.com/payment-gateway?id={$response->data->transactionId}&token={$response->data->token}&userIdentifier={$this->username}";
            //this is the final url format that the customer will be redirected to.
        }
    }
}
