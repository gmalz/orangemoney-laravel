<?php

namespace OrangeMoney\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Exceptions\HttpResponseException;

class OrangeMoney
{
    private $client;
    private $token;

    private $auth_header;
    private $credentials;
    private $merchant_key;
    private $return_url;
    private $cancel_url;
    private $notif_url;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('orangemoney.url'),
        ]);

        $this->auth_header = config('orangemoney.auth_header');
        $this->merchant_key = config('orangemoney.merchant_key');
        $this->return_url = config('orangemoney.return_url');
        $this->cancel_url = config('orangemoney.cancel_url');
        $this->notif_url = config('orangemoney.notif_url');

        $this->setToken();
    }

    public function methodCall(string $method, string $endpoint, array $options)
    {
        try {
            $response = $method === 'post'
                ? $this->client->request($method, $endpoint, $options)
                : $this->client->request($method, $endpoint);

            return $response;
        } catch(ClientException $clientException) {
            report($clientException);
            return new HttpResponseException(response($clientException->getMessage(), $clientException->getCode()));
        }
    }

    public function getToken()
    {
        $options = [
            'headers'=> [
                'Authorization' => 'Basic ' . $this->auth_header,
                'Accept' => config('orangemoney.header.content_type'),
            ],
            'form_params' => [
                'grant_type' => 'client_credentials',
            ],
        ];

        return $this->methodCall('post', config('orangemoney.token'), $options);
    }

    public function getPayment(string $token, $data)
    {
        $body = [
            'merchant_key' => $this->merchant_key,
            'currency' => 'OUV',
            'order_id' => 'OM_0'.rand(100000, 900000).'_00'.rand(10000, 90000),
            'amount' => 0,
            'return_url' => $this->return_url,
            'cancel_url' => $this->cancel_url,
            'notif_url' => $this->notif_url,
            'lang' => 'fr',
        ];

        $options = [
            'headers'=> [
                'Authorization' => 'Bearer '.$token,
                'Accept' => config('orangemoney.header.content_type'),
                'Content-Type' => config('orangemoney.header.content_type'),
            ],
            'body' => json_encode(array_merge($body, $data)),
        ];

        return $this->methodCall('post', config('orangemoney.endpoints.web_payment'), $options);
    }

    public function getTransactionStatus(string $token, array $data)
    {
        $body = [
            'order_id' => $data['order_id'],
            'amount' => $data['amount'],
            'pay_token' => $data['pay_token'],
        ];

        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($body),
        ];

        return $this->methodCall('post', config('orangemoney.endpoints.transaction_status'), $options);
    }

    public function setToken()
    {
        $data = json_decode((string) $this->getToken()->getBody(), true);
        $this->token = $data['access_token'];
    }

    public function webPayment($data)
    {
        $response = $this->client->getPayment($this->token, $data);
        
        if (is_object($response)) {
            return json_decode((string)$response->getBody(), true);
        } else {
            return $response;
        }
    }

    public function checkTransactionStatus($orderId, $amount, $pay_token)
    {
        $data = [
            'order_id' => $orderId,
            'amount' => $amount,
            'pay_token' => $pay_token,
        ];

        $response = $this->getTransactionStatus($this->token, $data);

        if (is_object($response)) {
            return json_decode((string)$response->getBody(), true);
        } else {
            return $response;
        }
    }
}