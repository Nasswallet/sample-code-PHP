<?php

use GuzzleHttp\Client;
use Guzzle\Http\EntityBody;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;
use GuzzleHttp\Exception\RequestException;


class Payment

{

    private $merchantToken = "Basic <TOKEN>"; 
    private $username = "";  //merchant username
    private $password = "";    //merchant password
    private $grantType = "<PASSWORD>";
    private $transactionPin = ""; //Merchant MPIN   
    private $orderId = "<ORDER_ID>";   //will be provided by the merchant
    private $amount = "10";        // will be provided by the merchant   
    private $languageCode = "en";
    private $client;


    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://uatgw1.nasswallet.com/payment/transaction/',
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
             return $response->message;
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
            
            echo "https://uatcheckout1.nasswallet.com/payment-gateway?={$response->data->transactionId}&token={$response->data->token}&userIdentifier={$this->username}";
                
            //this is the final url format that the customer will be redirected to.
        }else {
            return "something went wrong!";
        }
        
    }
 
}
