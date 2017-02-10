<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 10.02.2017
 * Time: 10:53
 */

namespace brainysoft\testmultibase;


class PersonSnils
{
    public $snils = '';

    public function __construct($snils = '')
    {
        if( !empty($snils) && !$this->testSnils($snils) ) {
            throw new \InvalidArgumentException('Снилс не проходит проверку');
        }
        $this->snils = $snils;
    }

    /**
     * @param string $snils
     * @return bool
     */
    public function testSnils($snils = '') {
        $snils = trim($snils);
        if( !preg_match('/^([\\d]{3}-[\\d]{3}-[\\d]{3})\\s+([\\d]{2})$/', $snils, $a) ) {
            return false;
        }
        $sDigits = str_replace('-', '', $a[1]);
        $sCode = $a[2];
        $calcCode = 0;
        for($i = 0, $n = strlen($sDigits); $i < $n; $i++) {
            $calcCode += intval(substr($sDigits, $i, 1)) * ($n - $i);
        }

        while ($calcCode > 99) {
            if( in_array($calcCode, [100, 101]) ) {
                $calcCode = 0;
            }
            else {
                $calcCode = $calcCode % 101;
            }
        }

        if( $sCode != sprintf('%02d', $calcCode) ) {
            return false;
        }

        return true;
    }

    public function calculateCode($number = '') {
        $sDigits = str_replace('-', '', $number);
        $calcCode = 0;
        for($i = 0, $n = strlen($sDigits); $i < $n; $i++) {
            $calcCode += intval(substr($sDigits, $i, 1)) * ($n - $i);
        }

        while ($calcCode > 99) {
            if( in_array($calcCode, [100, 101]) ) {
                $calcCode = 0;
            }
            else {
                $calcCode = $calcCode % 101;
            }
        }
        return $calcCode;
    }

    /**
     * @return string
     */
    public function generateSnils() {
        mt_srand(time());
        $sNum = mt_rand(100, 999) . '-' . mt_rand(100, 999) . '-' . mt_rand(100, 999);

        return $sNum . ' ' . sprintf('%02d', $this->calculateCode($sNum));
    }

}