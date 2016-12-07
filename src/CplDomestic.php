<?php

namespace Servality\CouriersPlease;

use GuzzleHttp\Client;

class CplDomestic
{

    /**
     * @var string $auth
     */

    private $auth;

    /**
     * @var bool $debug
     */

    private $debug = false;

    /**
     * @var bool $http_errors
     */

    private $http_errors = false;

    /**
     * @var string
     */

    private $accept = 'application/json';

    /**
     * CplDomestic constructor.
     * @param $auth
     * @param $options
     */

    public function __construct($auth, $options = [])
    {
        if(is_array($auth)){
            $username = isset($auth['username']) ? $auth['username'] : null;
            $token = isset($auth['token']) ? $auth['token'] : null;
        }

        $this->auth = 'Basic '.base64_encode($username.':'.$token);

        if(is_array($options)){
            $this->debug = isset($options['debug']) ? $options['debug'] : false;
            $this->accept = isset($options['accept']) ? 'application/'.$options['accept'] : 'application/json';
            $this->http_errors = isset($options['http_errors']) ? 'application/'.$options['http_errors'] : false;
        }
    }

    /**
     * @param string $method
     * @param string $uri
     * @param string $body
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */

    private function sendRequest(string $method, string $uri, string $body = ''){

        $host = 'api-test.couriersplease.com.au';
        $api_version = 'v1';
        $baseUri = 'https://'.$host.'/'.$api_version.'/';

        $client = new Client([
            'base_uri' => $baseUri,
            'timeout' => 2.0
        ]);

        $response = $client->request($method, $uri, [
            'debug' => $this->debug,
            'http_errors' => $this->http_errors,
            'headers' => [
                'Host' => $host,
                'Accept' => $this->accept,
                'Authorization' => $this->auth
            ],
            'body' => $body
        ]);

        return $response;
    }

    /**
     * @param string $trackingCode
     * @return array
     */

    public function tracking($trackingCode){
        $uri = 'domestic/locateParcel?trackingCode='.$trackingCode;
        $response = $this->sendRequest('GET', $uri);
        $trackingInfo = json_decode($response->getBody(), true); //Return an associative array from the returned json

        return $trackingInfo;
    }

    /**
     * @param string $suburbOrPostcode
     * @return array
     */

    public function location($suburbOrPostcode){
        $uri = 'locations?suburbOrPostcode='.$suburbOrPostcode;
        $response = $this->sendRequest('GET', $uri);
        return json_decode($response->getBody(), true);
    }

    /**
     * @param string $data
     * @return array
     */

    public function pickup($data){
        $uri = 'domestic/bookPickup';
        $response = $this->sendRequest('POST', $uri, $data);
        return json_decode($response->getBody(), true);
    }

    /**
     * @param string $data
     * @return array
     */

    public function quote($data){
        $uri = 'domestic/quote';
        $response = $this->sendRequest('POST', $uri, $data);
        return json_decode($response->getBody(), true);
    }

    /**
     * @param string $data
     * @return array
     */

    public function validateShipment($data){
        $uri = 'domestic/shipment/validate';
        $response = $this->sendRequest('POST', $uri, $data);
        return json_decode($response->getBody(), true);
    }

    /**
     * @param string $data
     * @return array
     */

    public function createShipment($data){
        $uri = 'domestic/shipment/create';
        $response = $this->sendRequest('POST', $uri, $data);
        return json_decode($response->getBody(), true);
    }

    /**
     * @param string $consignmentNumber
     */

    public function label($consignmentNumber){
        $uri = 'domestic/shipment/label?consignmentNumber='.$consignmentNumber;
        $response = $this->sendRequest('GET', $uri);
        //Base64 encoded PDF
    }

}