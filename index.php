<?php

header('Content-type: text/html; charset=utf-8');


use GuzzleHttp\Client;
use Guzzle\Http\EntityBody;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;
use GuzzleHttp\Exception\RequestException;


require 'vendor/autoload.php';

require 'Payment.php';




if (isset($_POST['action']) == 'pay') {
    $payment = new Payment();
    return $payment->makePayment();
}


 


    
 
require 'index.view.php';