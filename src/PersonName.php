<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 06.02.2017
 * Time: 14:44
 */

namespace brainysoft\testmultibase;


class PersonName
{
    public $firstName; // Имя
    public $lastName; // Фамилия
    public $patronymic; // Отчество

    public function __construct($firstName, $lastName, $patronymic = '')
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->patronymic = $patronymic;
    }

    public function getFullName() {
        return $this->lastName
            . ' '
            . $this->firstName
            . (empty($this->patronymic) ? '' : (' ' . $this->patronymic));
    }

}