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

class BatchSender
{

    /**
     * @var Client $client
     */
    private $client = null;

    /** @var array $aRequest */
    private $aRequest = [];

    /** @var array $aResponse */
    private $aResponse = [];

    /** @var array $aErrors */
    private $aErrors = [];

    /** @var array $aHeaders */
    private $aHeaders = [];

    private $oError = null;

    public function __construct(Client $client, $headers = [])
    {
        $this->client = $client;
        $this->aHeaders = $headers;
    }

    /**
     * @param Config $config
     * @param string $method
     * @param string $path
     * @param array $data
     * @param array $headers
     *
     * $return array
     *
     */
    public function addRequest(Config $config, $method = 'GET', $path = '', $data = [], $headers = []) {
        $aHeaders = array_merge(
            $this->aHeaders,
            [
                'bsauth' => $config->bsauth,
                'customer-key' => $config->customer,
                'user-key' => $config->userkey,
            ],
            $headers
        );

        $method = strtoupper($method);
        $url = $config->baseurl . $path;

        if( $method == 'GET' ) {
            $sParam = http_build_query($data);
            if( !empty($sParam) ) {
                $url .= ((strpos($url, '?') === false) ? '?' : '&') . $sParam;
            }
            $body = null;
        }
        else {
            $body = json_encode($data);
        }

        $this->aRequest[] = [
            'url' => $url,
            'method' => $method,
            'headers' => $aHeaders,
            'body' => $body,
        ];
//        yield new Request($method, $url, $aHeaders, $body);
    }

    /**
     *
     */
    public function clearRequests() {
        $this->aRequest = [];
        $this->aResponse = [];
        $this->aErrors = [];
    }

    /**
     *
     */
    public function send() {
        $client = new Client();

        $me = $this;
        $countData = $this->getCount();

        $this->aResponse = [];
        $this->aErrors = [];

        if( $countData == 0 ) {
            return;
        }

        $requests = function($nData) use ($me, $countData) {
            for($i = 0; $i < $nData; $i++) {
                $data = $me->aRequest[$i % $countData];
                $oReq = new Request($data['method'], $data['url'], $data['headers'], $data['body']);
                yield $oReq;
            }
        };

        $pool = new Pool($client, $requests($countData), [
            'concurrency' => $countData,
            'fulfilled' => function ($response, $index) use($me) {
                /** @var \GuzzleHttp\Psr7\Response $response */
                $me->aResponse[$index] = $response;
            },
            'rejected' => function ($reason, $index) use($me) {
                $me->aErrors[$index] = $reason;
                // this is delivered each failed request
            },
        ]);

// Initiate the transfers and create a promise
        $promise = $pool->promise();

// Force the pool of requests to complete.
        $promise->wait();
    }

    /**
     * @return int
     */
    public function getCount() {
        return count($this->aRequest);
    }

    /**
     * @param int $index
     */
    public function getResult($index) {
        $nCount = $this->getCount();
        if( $nCount == 0 ) {
            return [];
        }

        $index = $index % $nCount;

        return [
            'request' => $this->aRequest[$index],
            'response' => isset($this->aResponse[$index]) ? $this->aResponse[$index]->getBody()->getContents() : null,
            'headers' => isset($this->aResponse[$index]) ? $this->aResponse[$index]->getHeaders() : null,
            'error' => isset($this->aErrors[$index]) ? $this->aErrors[$index] : null,
            'customer' => $this->aRequest[$index]['headers']['customer-key'],
        ];
    }

}