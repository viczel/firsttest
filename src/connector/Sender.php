<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 07.02.2017
 * Time: 10:42
 */

namespace brainysoft\testmultibase\connector;

use brainysoft\testmultibase\connector\Config;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;

class Sender
{
    /**
     * @var \brainysoft\testmultibase\connector\Config $config
     */
    private $config = null;

    private $client = null;

    /** @var array $aHeaders */
    private $headers = [];

    private $oError = null;

    public $oContext = null;

    private $response = null;

    public function __construct(Config $config, $headers = [], $client = null)
    {
        $this->config = $config;
        $this->headers = $headers;
        $this->client = ($client === null) ? new Client() : $client;
    }

    /**
     *
     * @param string $method
     * @param string $path
     * @param array $data
     * @param array $headers
     *
     * $return array
     *
     */
    public function send($method = 'GET', $path = '', $data = [], $headers = []) {
        $headers = array_merge(
            $this->headers,
            [
                'bsauth' => $this->config->bsauth,
                'customer-key' => $this->config->customer,
                'user-key' => $this->config->userkey,
            ],
            $headers
        );

        $method = strtoupper($method);
        $url = $this->config->baseurl . $path;

        if( $method == 'GET' ) {
            $sParam = http_build_query($data);
            if( strlen($sParam) > 0 ) {
                $url .= ((strpos($url, '?') === false) ? '?' : '&') . $sParam;
            }
            $body = null;
        }
        else {
            $body = json_encode($data);
        }

        $this->oContext = [
            'method' => $method,
            'url' => $url,
            'headers' => $headers,
            'body' => $body,
        ];

        $this->oError = null;
        $request = new Request($method, $url, $headers, $body);
        $content = '';

        try {
            $response = $this->client->send($request);
//            print_r($response->getBody());
            $content = $response->getBody()->getContents();
            $obReturn = json_decode($content);
//            echo "status = {$obReturn->status}\n";
//            echo substr(print_r($obReturn, true), 0, 300) . "\n...................\n";
            if( $obReturn->status != 'ok' ) {
//                print_r($aData);
                $this->oError = $obReturn;
                $response = null;
            }
            else {
                $response = $obReturn;
            }
        }
        catch (\Exception $e ) {
            if( method_exists($e, 'getResponse') ) {
                $e = json_decode($e->getResponse()->getBody()->getContents());
            }
            $this->oError = $e;
            $response = null;
        }

        $this->response = $response;
//        echo substr(print_r($this->response, true), 0, 300) . "\n...................\n";
        return $this->response;
    }

    /**
     * @return bool
     */
    public function hasError() {
        return $this->oError !== null;
    }

    /**
     * @return object|null
     */
    public function getError() {
        return $this->oError;
    }

    public function getData() {
        return $this->response;
//        if( $this->response === null ) {
//            return [];
//        }
//
//        return json_decode($this->response->getBody()->getContents());
    }

    public function convertTo866($s = '') {
        return iconv('UTF-8', 'CP866', $s);
    }

}