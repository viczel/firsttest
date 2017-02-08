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
        $name = new PersonName($this->oFaker->firstName, $this->oFaker->lastName);
        $bithday = new PersonBirthday($this->oFaker->date('Y-m-d', '1995-10-17'), $this->oFaker->city);

        $oPerson = Person::createMale(
            $name,
            $bithday
        );

        $oPerson->addPhone($this->oFaker->numerify('79#########'));


        /*
         * Без этих полей лид создается
         */
        if( isset($params['products']) && !empty($params['products']) ) {
            $oPerson->creditProductId = $params['products'][$this->oFaker->numberBetween(0, count($params['products']) - 1)];
        }

        $oPerson->setCredit(
            new Credit(
                $this->oFaker->randomDigitNotNull * 10000,
                $this->oFaker->numberBetween(5, 15),
                $this->oFaker->numberBetween(2, 15) * 10
            )
        );

        return $oPerson;
    }
}