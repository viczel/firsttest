<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 02.02.2017
 * Time: 10:52
 */

namespace brainysoft\testmultibase;


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
     * @param $nData
     * @return array
     */
    public function getLeads($nData) {
        $aResult = [];

        for($i = 0; $i < $nData; $i++) {
            $aResult[] = $this->oFaker->firstName();
        }

        return $aResult;
    }

}