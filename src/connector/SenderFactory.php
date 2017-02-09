<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 09.02.2017
 * Time: 12:11
 */

namespace brainysoft\testmultibase\connector;

use brainysoft\testmultibase\connector\Sender;
use brainysoft\testmultibase\connector\ConfigFactory;


class SenderFactory
{
    public static function createSender($customerId) {
        $oConf = ConfigFactory::createConfig($customerId);
        return new Sender($oConf, ["Content-Type" => "application/json",]);
    }
}