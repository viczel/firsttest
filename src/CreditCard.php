<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 07.02.2017
 * Time: 13:25
 */

namespace brainysoft\testmultibase;


class CreditCard
{
    public $cardNumber = ''; // Номер банковской карты, только цифры, без пробелов и других знаков
    public $cardHolder = ''; // Держатель карты.
    public $validThruMonth = ''; // Срок действия карты месяц, формат: ММ
    public $validThruYear = ''; // Срок действия карты год, формат: ГГ
    public $cardCvc = ''; // CVC код, 3 цифры

    /**
     * CreditCard constructor.
     * @param string $cardNumber
     * @param string $cardHolder
     * @param string $validThruMonth
     * @param string $validThruYear
     * @param string $cardCvc
     */
    public function __construct($cardNumber, $cardHolder = '', $validThruMonth = '', $validThruYear = '', $cardCvc = '')
    {
        $this->cardNumber = $cardNumber;
        $this->cardHolder = $cardHolder;
        $this->validThruMonth = $validThruMonth;
        $this->validThruYear = $validThruYear;
        $this->cardCvc = $cardCvc;
    }
}