<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 07.02.2017
 * Time: 12:49
 */

namespace brainysoft\testmultibase;

use brainysoft\testmultibase\ExtraField;

abstract class BaseLead implements \brainysoft\testmultibase\LeadDataInterface
{
    const CHANNEL_SITE = 'SITE';            //  для лидов, поступивших с сайта или личного кабинета
    const CHANNEL_GENERATOR = 'GENERATOR';  //  для лидов, поступивших с лидогенераторов
    const CHANNEL_PRESCORE = 'PRESCORE';    //  лиды, поступившие по этому каналу будут направлены в
                                            // систему принятия решения для определения кредитного
                                            // лимита клиента. Созданные контракты нельзя будет выдать.

    public $channel = self::CHANNEL_SITE; // Канал поступления

    public $naturalPerson = true;   // Физическое лицо

    public $addresses = null;       // Коллекция привязанных дополнительных адресов.

    public $managerId = null;       // Идентификатор кредитного специалиста
    public $creditProductId = null; // Идентификатор кредитного продукта (тарифа), по которому клиент хочет получить займ
    public $extraFields = [];       // В этот контейнер добавляются те поля, которые необходимы при создании Контракта,
                                    // Клиента, Заявки, Контракта (являются обязательными для заполнения),
                                    // но отсутствуют в основных полях ЛИДа.

    public $ipAddress = "";   // ip адрес
    public $macAddress = "";  // МАС адрес

    public $ipAndRegionMatch = false;      // Совпадает ли заявленный регион в фактическом адресе (адресе проживания)  с IP адресом заявителя
    public $ipAndRegAddressMatch = false;  // Совпадает ли заявленный регион в адресе регистрации  с IP адресом заявителя

    public $mobilePhoneCheck = false;      // Флаг верификации телефона.
    public $rosfinmonitoringCheck = true;  //
    public $ufmsCheck = false;             //
    public $approvedByScorista = false;    //

    public $currentStatus = null;          // Текущий статус лида.
    public $orderCode = '';                // Код заказа поступивший от партнеров

    protected $phones = [];       // телефоны
    protected $emails = [];       // e-mail's

    /**
     * @param $channel
     */
    public function setChannel($channel) {
        if( !in_array($channel, [self::CHANNEL_SITE, self::CHANNEL_GENERATOR, self::CHANNEL_PRESCORE]) ) {
            throw new \InvalidArgumentException('Канал поступления лида не совпадает с результирующими');
        }
        $this->channel = $channel;
    }

    /**
     * @param string $sPhone
     */
    public function addPhone($sPhone = '') {
        $sPhone = trim(preg_replace('/[^\\d]/', '', $sPhone));
        if( !preg_match('/^[\\d]{10,}$/', $sPhone) ) {
            throw new \InvalidArgumentException('Телефон должен состоять как миниммум из 10 циферок');
        }
        if( !in_array($sPhone, $this->phones) ) {
            $this->phones[] = $sPhone;
        }
    }

    /**
     * @param string $email
     */
    public function addEmail($email = '') {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if( !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
            throw new \InvalidArgumentException('Email не проходит проверку');
        }
        if( !in_array($email, $this->emails) ) {
            $this->emails[] = $email;
        }
    }

    /**
     * @return array
     */
    public function getAllPhones() {
        return $this->phones;
    }

    /**
     * @return array
     */
    public function getAllEmails() {
        return $this->email;
    }

    /**
     *
     * Для выдачи полей объекта
     *
     * @return array
     */
    public function getPhones() {
        return [
            'mobilePhone' => empty($this->phones) ? "" : $this->phones[0],
        ];
    }

    /**
     *
     * Для выдачи полей объекта
     *
     * @return array
     */
    public function getEmails() {
        return [
            'email' => empty($this->email) ? "" : $this->email[0],
        ];
    }

    /**
     * @param string $sKey
     * @param string $sValue
     */
    public function setExtraField($sKey, $sValue = '') {
        foreach ($this->extraFields As $k=>$v) {
            /** @var ExtraField $v */
            if( $v->isEqual($sKey) ) {
                $this->extraFields[$v] = new ExtraField($sKey, $sValue);
                return;
            }
        }
        $this->extraFields[] = new ExtraField($sKey, $sValue);
    }

    /**
     *
     * LeadDataInterface
     *
     * @return array
     */
    public function getLeadData() {
        return $this->getDataRecurcive($this);
    }

    /**
     * @param $ob
     */
    public function getDataRecurcive($ob) {
        $aFields = $this->getProperties($ob, \ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PRIVATE);
        $aRet = [];

        foreach ($aFields As $fld) {
            /** @var \ReflectionProperty $fld */
            $sName = $fld->getName();

            if( $fld->isPrivate() || $fld->isProtected() ) {
                $fld->setAccessible(true);
            }

            $oPrivate = $fld->getValue($ob);
            $sGetter = 'get' . ucfirst($sName);
            $aRet['getter'] = isset($aRet['getter']) ? ($aRet['getter'] . ' ' . $sGetter) : $sGetter;
            if( method_exists($ob, $sGetter) ) {
                $aRet = array_merge($aRet, $ob->{$sGetter}());
            }
            else if( is_object($oPrivate) ) {
                $aRet = array_merge($aRet, $this->getDataRecurcive($oPrivate));
            }
            else {
                $aRet[$sName] = $oPrivate; // $ob->{$sName};
            }

        }
        return $aRet;
    }

    /**
     * @param $ob
     * @param int $nTypes
     * @return \ReflectionProperty[]
     */
    public function getProperties($ob, $nTypes = \ReflectionProperty::IS_PUBLIC) {
        $reflect = new \ReflectionClass($ob);
        $aFields = $reflect->getProperties($nTypes);
        return $aFields;
    }

    /*

            $lead = [
------------------------------------------------------------------------------------------
BaseLead
                "channel" => "SITE",
                "naturalPerson" => true,

                "ipAddress" => "",
                "macAddress" => "",

                "managerId" => null,
                "creditProductId" => null,

                "extraFields" => [],

                "mobilePhone" => "",
                "email" => "",

                "addresses" => null

                "ipAndRegionMatch" => false,
                "ipAndRegAddressMatch" => false,

                "mobilePhoneCheck" => false,
                "rosfinmonitoringCheck" => true,
                "ufmsCheck" => false,
                "approvedByScorista" => false,

                "orderCode" => null,

------------------------------------------------------------------------------------------
Person
                "sexId" => null,
                "inn" => "",
                "snils" => "",

                "firstName" => "-",
                "lastName" => "-",
                "patronymic" => "-",

                "birthDate" => null,
                "birthPlace" => "",

                "passport" => null,

                "cardNumber" => "",
                "cardHolder" => "",
                "validThruMonth" => "",
                "validThruYear" => "",
                "cardCvc" => "",

                "childrenCount" => 0,
                "adultChildrenCount" => 0,
                "dependentsCount" => 0,

                "addressData" => []
                "registrationAddressData" => []

------------------------------------------------------------------------------------------
                "title" => "", // Наименование организации (Для Юридического лица)
                "registrationNumber" => "", // Номер ОГРН (Для Юридического лица)

                "relatives" => [],

                "leadDebts" => [],
                "amount" => 0,
                "period" => 0,
                "periodUnit" => "DAYS",
                "gettingMoneyMethodId" => null,
                "goods" => [],

                "storeTypeId" => null,

                "employerTitle" => "",
                "employerInn" => "",

                "meanIncome" => 0.0,
                "averageMonthlyCost" => 0.0,
                "monthlyCreditPayment" => 0.0,
                "closedCreditsCount" => 0,
                "delinquencyCount" => 0,
                "payedDelinquencyCount" => 0,
                "writtenDelinquencyCount" => 0,
                "activeCreditsCount" => 0,
                "activeCreditsAmount" => 0.0,
                "activeDelinquencyAmount" => 0.0,

                "denialReasonId" => null,

                "storeCode" => null,


                "deviceTypeId" => null,
                "referralLink" => "",
                "intRate" => 0,

                "totalAmount" => 0,
                "totalAmountDelinq30" => 0,

            ];

    */
}