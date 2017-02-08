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

    /** @var array $aHeaders */
    private $headers = [];

    private $oError = null;

    public $oContext = null;

    private $response = null;

    public function __construct(Config $config, $headers = [])
    {
        $this->config = $config;
        $this->headers = $headers;
    }

    /**
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
            $url .= ((strpos($url, '?') === false) ? '?' : '&') . http_build_query($data);
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
        $client = new Client();
        $request = new Request($method, $url, $headers, $body);

        try {
            $response = $client->send($request);
        }
        catch (\Exception $e ) {
            $this->oError = $e;
            $response = null;
        }

        $this->response = $response;
        return $response;
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
        if( $this->response === null ) {
            return [];
        }

        return json_decode($this->response->getBody()->getContents());
    }

}