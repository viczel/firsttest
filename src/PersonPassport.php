<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 06.02.2017
 * Time: 12:45
 */

namespace brainysoft\testmultibase;


class PersonPassport
{
    public $id;                     //	Паспорт. Идентификационный номер.
    public $seria;                  //	Паспорт серия.
    public $no;                     //	Паспорт номер.
    public $issueDate;              //	Дата выдачи паспорта.
    public $closeDate;              //	Дата истечения паспорта.
    public $manager;                //	Паспорт выдан.
    public $subdivisionCode;        //	Код подразделения.
    public $complementaryDocTypeId; //	Идентификатор типа документа. Метод получения списка "Типа документа".
    
    public function __construct(
        $id = null,
        $seria = null,
        $no = null,
        $issueDate = null,
        $closeDate = null,
        $manager = null,
        $subdivisionCode = null,
        $complementaryDocTypeId = 102126)
    {
        $this->id = $id;
        $this->seria = $seria;
        $this->no = $no;
        $this->issueDate = $issueDate;
        $this->closeDate = $closeDate;
        $this->manager = $manager;
        $this->subdivisionCode = $subdivisionCode;
        $this->complementaryDocTypeId = $complementaryDocTypeId;

        $aTypes = [
            [
                'id' => 102121,
                'name' => 'Водительское удостоверение',
            ],
            [
                'id' => 102122,
                'name' => 'СНИЛС',
            ],
            [
                'id' => 102123,
                'name' => 'Заграничный паспорт',
            ],
            [
                'id' => 102126,
                'name' => 'Паспорт',
            ],
            [
                'id' => 102127,
                'name' => 'Другой документ',
            ],
            [
                'id' => 102128,
                'name' => 'Нет',
            ],
        ];

        $bIsCorrectType = array_reduce(
            $aTypes,
            function($carry, $el) use($complementaryDocTypeId) { return ($complementaryDocTypeId == $el['id']) || $carry; },
            false
        );

        if( !$bIsCorrectType ) {
            throw new \InvalidArgumentException('Не тот тип у документа');
        }

    }

}