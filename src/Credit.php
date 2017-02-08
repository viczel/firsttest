<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 08.02.2017
 * Time: 12:17
 */

namespace brainysoft\testmultibase;


class Credit
{
    const CREDIT_PERIOD_DAYS = 'DAYS';
    const CREDIT_PERIOD_WEEKS = 'WEEKS';

    public $amount = 0;          // Сумма займа
    public $intRate = 0;         // Процентная ставка
    public $period = 0;          // Запрашиваемый срок займа
    public $periodUnit = self::CREDIT_PERIOD_DAYS; // Срок займа (DAYS;WEEKS)

    public function __construct($amount = 0, $intRate = 0, $period = 0, $periodUnit = "DAYS")
    {
        if( !in_array($periodUnit, [self::CREDIT_PERIOD_DAYS, self::CREDIT_PERIOD_WEEKS]) ) {
            throw new \InvalidArgumentException('Единицы измерения периода не совпадают с возможными');
        }
        $this->amount = $amount;
        $this->intRate = $intRate;
        $this->period = $period;
        $this->periodUnit = $periodUnit;
    }

}