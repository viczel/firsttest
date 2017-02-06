<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 06.02.2017
 * Time: 14:47
 */

namespace brainysoft\testmultibase;


class PersonBirthday
{

    public $birthDate; // Дата рождения
    public $birthPlace; // Место рождения

    public function __construct($birthDate = null, $birthPlace = '')
    {
        $this->birthDate = $birthDate;
        $this->birthPlace = $birthPlace;

        if( !empty($birthDate) ) {
            if( !preg_match('/([\\d]{4}-[\\d]{2}-[\\d]{2})/', $birthDate) ) {
                throw new \InvalidArgumentException("День рождения должен быть в формате yyyy-mm-dd");
            }

            try {
                $date = new \DateTime($birthDate);
            } catch (\Exception $e) {
                throw new \InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
            }

            if( $date->getTimestamp() > time() ) {
                throw new \InvalidArgumentException("День рождения не может быть в будущем");
            }
        }

    }

}