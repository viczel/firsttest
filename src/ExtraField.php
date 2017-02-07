<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 07.02.2017
 * Time: 13:10
 */

namespace brainysoft\testmultibase;


class ExtraField
{
    public $key = '';
    public $value = '';

    public function __construct($key, $value)
    {
        if( empty($key) ) {
            throw new \InvalidArgumentException('Ключ дополнительного параметра нужно указать');
        }

        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function isEqual($key = '') {
        return ($this->key == $key);
    }
}