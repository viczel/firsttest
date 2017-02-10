<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 02.02.2017
 * Time: 10:52
 */

namespace brainysoft\testmultibase;

use brainysoft\testmultibase\Person;
use brainysoft\testmultibase\PersonName;
use brainysoft\testmultibase\PersonBirthday;
use brainysoft\testmultibase\Credit;
use brainysoft\testmultibase\PersonPassport;
use brainysoft\testmultibase\PersonSnils;

class DataGenerator
{
    /**
     * @var \Faker\Generator $oFaker
     */
    private $oFaker = null;

    public function __construct($oFaker)
    {
        $this->oFaker = $oFaker;
    }

    /**
     *
     * Генерим данные для пользователей
     *
     * @param $nData
     * @return array
     */
    public function getUsers($nData) {
        return range(1, $nData);
    }

    /**
     *
     * Генерим лидов
     *
     * @param int $nData       кол-во лидов
     * @param array $params    параметры для генерации лида
     * @return array
     */
    public function getLeads($nData, $params = []) {
        $aResult = [];

        for($i = 0; $i < $nData; $i++) {
            $aResult[] = $this->generateOneLead($params);
        }

        return $aResult;
    }

    /**
     * @param array $params
     *       - ['products'] - массив id кредитных продуктов для установки лиду
     * @return \brainysoft\testmultibase\Person
     */
    public function generateOneLead($params = []) {
        /*
         * Для успешного создания лида нужны следующие поля:
         */
//        $oFaker->lastName . ' ' . $oFaker->middleNameMale . ' ' . $oFaker->firstNameMale

        $sDop = '';
        if( isset($params['customerid']) ) {
            $sDop = ' ' . $params['customerid'];
        }

        $name = new PersonName($this->oFaker->firstNameMale(), $this->oFaker->lastName(), $this->oFaker->middleNameMale());
        $bithday = new PersonBirthday($this->oFaker->date('Y-m-d', '1995-10-17'), $this->oFaker->city . $sDop);
//        echo iconv('UTF-8', 'CP866', print_r($name, true) . ' ' . $this->oFaker->firstNameMale() . "\n");

//        $oPerson = Person::createMale(
//            $name,
//            $bithday
//        );

        $oPerson = new Person(
            $name,
            $bithday
        );
        $oPerson->setMale();

        $oPerson->addPhone($this->oFaker->numerify('79#########'));

        if( !empty($sDop) ) {
            $oPerson->addEmail(trim($sDop) . '.' . $this->oFaker->email);
        }

        /*
         * Без этих полей лид создается
         */
        if( isset($params['products']) && !empty($params['products']) ) {
            $oProduct = $this->getProduct($params['products']);

            $oPerson->creditProductId = $oProduct->id;
            $oPerson->setCredit(
                $this->createCreditForProduct($oProduct)
            );

//            $oPerson->creditProductId = $params['products'][$this->oFaker->numberBetween(0, count($params['products']) - 1)];
//            $oPerson->setCredit(
//                new Credit(
//                    $this->oFaker->randomDigitNotNull * 10000,
//                    $this->oFaker->numberBetween(5, 15),
//                    $this->oFaker->numberBetween(2, 15) * 10
//                )
//            );

        }

        /*
         * Без этих полей не запускается проверка
         */

        // -------------------------------- NO_PASSPORT_ERROR NO_PASSPORT_ISSUE_DATE_ERROR

        $t = $bithday->getDate();
        $dPassport = mktime(
            0,
            0,
            0,
            date('m', $t),
            date('j', $t) + $this->oFaker->numberBetween(15, 40),
            date('Y', $t) + 16
        );

        $oPerson->setPassport(
            new PersonPassport(
                null,
                $this->oFaker->numberBetween(1000, 9999),
                $this->oFaker->numberBetween(100000, 999999),
                date('Y-m-d', $dPassport),
                null,
                null,
                $this->oFaker->numberBetween(100, 999) . '-' . $this->oFaker->numberBetween(100, 999)
            )
        );

        // -------------------------------- LOAN_AMOUNT_GREATER_THAN_MAX_LOAN_AMOUNT_ERROR
        // после этого написал создание кредита по продукту
        // теперь статус проверки стал
        // [status] => TECH_FAULT
        // [info] => code: METADATA__EMPTY_REQUIRED_FIELD; message: snils

        $snils = new PersonSnils();

        $oPerson->snils = $snils->generateSnils();

        //  [status] => TECH_FAULT
        //  [info] => code: METADATA__EMPTY_REQUIRED_FIELD; message: gettingMoneyMethodId

        if( isset($params['gettingmoneymethod']) ) {
            $oPerson->gettingMoneyMethodId = $this->getGettingMoneyMethod($params['gettingmoneymethod']);
        }

        // тут у некоторых видов продукта не хватает данных, поэтому сделал там в фильтре только один продукт, который прошел

        return $oPerson;
    }

    /**
     * @param array $aProductList
     * @return array
     */
    public function getProduct($aProductList = []) {
        $aProductList = array_filter(
            $aProductList,
            function ($el) { return $el->id == 1013331; /* return $el->active; */ }
        );

        $aKeys = array_keys($aProductList);

        $usedKey = $aKeys[$this->oFaker->numberBetween(0, count($aKeys) -1)];

        return $aProductList[$usedKey];
    }


    /**
     * @param $obProduct
     * @return \brainysoft\testmultibase\Credit
     */
    public function createCreditForProduct($obProduct) {
        $nMinDays = $obProduct->minPeriod;
        $nMaxDays = $obProduct->maxPeriod;

        if( $nMaxDays == 0 ) {
            if( $nMinDays == 0 ) {
                $nMinDays = $this->oFaker->numberBetween(15, 30);
                $nMaxDays = $nMinDays + $this->oFaker->numberBetween(15, 30);
            }
            else {
                $nMaxDays = $nMinDays + $this->oFaker->numberBetween(15, 30);
            }
        }
        else {
            if( $nMinDays == 0 ) {
                $nMinDays = intval($nMaxDays / 2);
            }
        }

        $nMinSum = $obProduct->minLoanAmount;
        $nMaxSum = $obProduct->maxLoanAmount;

        if( $nMaxSum == 0 ) {
            if( $nMinSum == 0 ) {
                $nMinSum = $this->oFaker->numberBetween(10, 100) * 1000;
            }
            $nMaxSum = $nMinSum + $this->oFaker->numberBetween(15, 30) * 1000;
        }
        else {
            if( $nMinSum == 0 ) {
                $nMinSum = intval($nMaxSum / 2);
            }
        }

        $aData = [
            'id' => $obProduct->id,
            'period' => $this->oFaker->numberBetween($nMinDays, $nMaxDays),
            'sum' => $this->oFaker->numberBetween($nMinSum, $nMaxSum),
            'originalperiod' => $obProduct->minPeriod . ' .. ' . $obProduct->maxPeriod,
            'originalsum' => $obProduct->minLoanAmount . ' .. ' . $obProduct->maxLoanAmount,
        ];

        $ob = new Credit(
            $aData['sum'],
            1,
            $aData['period']
        );

        return $ob;
    }

    /**
     * @param $aMethods
     * @return mixed
     */
    public function getGettingMoneyMethod($aMethods) {
        $usedKey = $this->oFaker->numberBetween(0, count($aMethods) - 1);
        return $aMethods[$usedKey]->id;
    }

    /**
     * @param string $customerId
     * @return string
     */
    public function generateMac($customerId = '') {
        if( empty($customerId) ) {
            return $customerId;
        }

        $a = str_split($customerId, 2);

        return '';
    }
}