<?php

use GuzzleHttp\Client;
use Guzzle\Http\EntityBody;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;
use GuzzleHttp\Exception\RequestException;


class Payment

{

    private $merchantToken = ""; 
    private $username = "";  
    private $password = "";    
    private $grantType = "password";
    private $transactionPin = "";   
    private $orderId = "263626";     
    private $amount = "10";           
    private $languageCode = "en";
    private $client;


    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://uatgw.nasswallet.com/payment/transaction/',
            'timeout' => '1000'
        ]);
    }


    public function loginWithMerchantAccount()
    {     
        $payload = [
            "data" => [
                "username" => $this->username,
                "password" => $this->password,
                "grantType" => $this->grantType
            ]
        ];

        $response = json_decode($this->client->request('POST', 'login', [
            "headers" => ['authorization' => "$this->merchantToken"],
            'json' => $payload
            
        ])->getBody());

        if ($response->responseCode == 0 && $response->data->access_token) {
            $response =  $response->data->access_token;
            $this->makeTransaction($response);
        } else {
            return 0;
        }
    }


    public function makeTransaction($access_token)
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
