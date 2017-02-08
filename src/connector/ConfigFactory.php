<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 08.02.2017
 * Time: 13:09
 */

namespace brainysoft\testmultibase\connector;

use brainysoft\testmultibase\connector\Config;

class ConfigFactory
{
    static public $configs = [];

    static public function getCustomerConfig($customerId = '') {
        if( empty($customerId) ) {
            throw new \InvalidArgumentException('Для получения конфига кастомера нужно указать его Id');
        }

        if( !isset(self::$configs[$customerId]) ) {
            self::$configs[$customerId] = self::createConfig($customerId);
        }

        return self::$configs[$customerId];
    }

    static public function createConfig($customerId) {
        switch ($customerId) {
            case 'a1':
                $el = new Config($customerId, 'http://172.16.1.88:8082', '', 1);
                break;

            case 'fastmoney':
                $el = new Config($customerId, 'http://10.10.20.25:8082', 'beuwiQkjUkFKtyq49qJA9DgUKatE29iTfImlBiyk5OXMw/tPu6/oU9MlbSl1OIXIkC+0St/bF+KcBLFKmcsDLg==', 1);
                break;

            default:
                throw new \InvalidArgumentException('Нет конфига для кастомера ' . $customerId);
                break;
        }
        return $el;
    }

}