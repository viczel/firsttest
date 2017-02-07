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

    /**
     * @var Client $client
     */
    private $client = null;

    private $oError = null;

    public function __construct(Config $config, Client $client)
    {
        $this->config = $config;
        $this->client = $client;
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

}