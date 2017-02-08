<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 07.02.2017
 * Time: 10:37
 */

namespace brainysoft\testmultibase\connector;


class Config
{
    public $customer = '';
    public $bsauth = '';
    public $userkey = 1;
    public $baseurl = '';

    public function __construct($customer, $baseurl, $bsauth, $userkey )
    {
        $this->customer = $customer;
        $this->baseurl = $baseurl;
        $this->bsauth = $bsauth;
        $this->userkey = $userkey;
    }
}