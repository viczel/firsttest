<?php

namespace brainysoft\testmultibase;

use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;


class LeadCreator
{
    private $aHeaders = [
        "Content-Type" => "application/json",
//        'Accept-Language' => 'en-US,en;q=0.8,ru;q=0.6',
//        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
//        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.76 Safari/537.36',
//        'Referer' => 'https://www.yandex.ru/',
//        'Cookie' => 'z=s:l:8582f4e4da:1486031317486; b=sru%40pI.vRtaHm*eit%3DC5mLIgpYtDu%3E6%40Pqkk%7DH%3DQco%7Bc%2B2%40%25l%3Ajp!%40yw%2Bn0%2BIgs%3DbktWBxdUhxHRE%24)1p%5E%5BJy7H%2FLr%24DfnOq%26cQN%24oo!y-g7%3FI%7Bey; q=puLVXdt2qAmC7wB59VEyKpCuJacUSRCUYY3y6iXe+oDH66MEoHcxlUcDXL0KAzmxK6Y=; yandexuid=9397487271485935023; yandex_gid=213; _ym_uid=1485935003219658011; zm=m-white_bender.flex.webp.css-https-www%3Awww_jXWhgI14yf22UKGe0vOF2MCpxnQ%3Al; Session_id=3:1485935697.5.0.1485935697644:6rhD2Q:43.0|1130000023072254.0.2|158272.455248.1izUMeyRHcNzjKUpITNzuh4cw0k; sessionid2=3:1485935697.5.0.1485935697644:6rhD2Q:43.1|1130000023072254.0.2|158272.654402.4OUwRPipv74b21frDmnlHG9Ye5E; L=WlRTdkB+VUpBegZqQlRgfV9/UQZgX2ZbIygSIw0TeFo4OyQzBwZaQwsMLCAUVyxDXRo0.1485935697.12927.34592.8fdc955c9309f5b01aaa373afb3c103d; yandex_login=viktor.kozmin@brainysoft.ru; _ym_isad=2; q=yQZDyv0ZV2upDXEQbB4yKpCuMtnIcmnT7r2kys8lS6igeCgnD1zOApUo+AfsKyo0A3Y=; yabs-frequency=/4/0G000Em9arW00000/sInoS70eJW00/; yp=1488527024.ygu.1#1493711025.ww.1#1501799336.szm.1%3A1920x1080%3A1625x929#1801295697.udn.cDp2aWt0b3Iua296bWluQGJyYWlueXNvZnQucnU%3D#1486636118.flsh.1#1517567318.dsws.1#1517567318.dswa.0#1517567318.dwss.1; ys=udn.cDp2aWt0b3Iua296bWluQGJyYWlueXNvZnQucnU%3D#ymrefl.901E733C861F4E00#wprid.1486031480199340-1305457998661566342000582-man1-4032',
    ];

    private $url = '';
    private $pool = [];

    public function __construct($url, $aHeaders = [])
    {
        $this->url = $url;
        $this->aHeaders = array_merge($this->aHeaders, $aHeaders);
    }

    /**
     *
     * Создание пула запросов
     *
     * @param string $method
     * @param string $path
     * @param array $data
     *
     * в $data каждый элемент содержит поле headers для указания там дополнительных заголовков каждого запроса
     *
     */
    public function createConcurentRequests($method = '', $path = '', $data = []) {
        $url = $this->url;
        $headers = $this->aHeaders;
        $method = trim(strtoupper($method));

        if( empty($method) ) {
            throw new \HttpInvalidParamException('Method is empty');
        }
        $requests = function ($total) use($url, $path, $method, $headers, $data) {
            $uri = $url . $path;
            $nData = count($data);

            $body = '';
            for ($i = 0; $i < $total; $i++) {
                $element = $data[$i % $nData];
                $adr = $uri;
                $aHeaders = array_merge(
                    $headers,
                    isset($element['headers']) ? $element['headers'] : []
                );
                if( $method  'POST' )
//                yield $adr;
                yield new Request($method, $adr, $aHeaders, $body);
            }
        };
    }

    public function createLeads($aLeads) {

        $client = new Client();

        $requests = function ($total) use($aLeads) {
            $uri = 'https://yandex.ru/search/?text=%%%%%%%&lr=213';
            $nLeads = count($aLeads);
//            $aLeads

            $body = '';
            $aHeaders = array_merge(
                $this->aHeaders,
                []
            );

            for ($i = 0; $i < $total; $i++) {
                $adr = str_replace('%%%%%%%', rawurlencode($aLeads[$i % $nLeads]), $uri);
//                yield $adr;
                yield new Request('GET', $adr, $aHeaders, $body);
            }
        };

        $pool = new Pool($client, $requests(2), [
            'concurrency' => 5,
            'fulfilled' => function ($response, $index) use($aLeads) {
                // this is delivered each successful response
                $nLeads = count($aLeads);
                /** @var \GuzzleHttp\Psr7\Response $response */
                $sBody = $response->getBody();
                $sBody = str_replace("\n", ' ', $sBody);
                if( preg_match('/<div class="serp\\-adv__found">([^<]+)<\\/div>/', $sBody, $a) ) {
//                if( preg_match('/Нашё[^\\s]+[\\s]+([\\d]+.*?)рез/', $sBody, $a) ) {
                    $sOut = iconv('UTF-8', 'CP866', $a[1]);
                    if( preg_match('/<div class="serp\\-adv__displayed">(.+?)<\\/div>/', $sBody, $a) ) {
                        $sOut .= ' ' . iconv('UTF-8', 'CP866', $a[1]);
                    }
                }
                else {
                    $p = strpos($sBody, 'serp-adv__found');
                    $sOut = ($p === false) ? $sBody : substr($sBody, $p, 200);
                }
//                echo str_repeat('-', 50) . "\nOK " . $index . ': ' . print_r($response, true) . "\n\n";
                echo str_repeat('-', 50) . "\nOK " . $index . ' ' . iconv('UTF-8', 'CP866', $aLeads[$index % $nLeads]) . ': ' . $sOut . "\n\n";
            },
            'rejected' => function ($reason, $index) {
                echo str_repeat('-', 50) . "\nERROR " . $index . ': ' . print_r($reason, true) . "\n\n";
                // this is delivered each failed request
            },
        ]);

// Initiate the transfers and create a promise
        $promise = $pool->promise();

// Force the pool of requests to complete.
        $promise->wait();

        echo "\n\nfinish\n\n";

        /*
Нашёлся 271 млн результатов
212 тыс. показов в месяц
         */
    }


}