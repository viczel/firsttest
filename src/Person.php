<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 06.02.2017
 * Time: 14:20
 */

namespace brainysoft\testmultibase;

use brainysoft\testmultibase\LeadDataInterface;
use brainysoft\testmultibase\PersonName;
use brainysoft\testmultibase\PersonBirthday;


class Person implements \brainysoft\testmultibase\LeadDataInterface
{
    const GENDER_MALE = 101251;    // Мужчина
    const GENDER_FEMALE = 101252;  // Женщина

    public $sexId = self::GENDER_MALE; // Пол Возможные значения:  101251 - Мужчина 101252 - Женщина


    public $inn;   // ИНН.
    public $snils; // СНИЛС.

    private $name = null;       // Имя
    private $birthdate = null;  // день рождения
    private $phones = [];       // телефоны
    private $emails = [];       // e-mail's
    private $passport = null;   // паспорт

    public function __construct(PersonName $name, PersonBirthday $birthdate, $inn = '', $snils = '')
    {
        $this->name = $name;
        $this->birthdate = $birthdate;

        $this->inn = $inn;
        $this->snils = $snils;
    }

    /**
     *
     * Создание мужчины
     *
     * @param \brainysoft\testmultibase\PersonName $name
     * @param \brainysoft\testmultibase\PersonBirthday $birthdate
     * @param string $inn
     * @param string $snils
     * @return Person
     */
    public static function createMale(PersonName $name, PersonBirthday $birthdate, $inn = '', $snils = '') {
        $ob = new self($name, $birthdate, $inn, $snils);
        $ob->setMale();
        return $ob;
    }

    /**
     *
     * Создание женщины
     *
     * @param \brainysoft\testmultibase\PersonName $name
     * @param \brainysoft\testmultibase\PersonBirthday $birthdate
     * @param string $inn
     * @param string $snils
     * @return Person
     */
    public static function createFemale(PersonName $name, PersonBirthday $birthdate, $inn = '', $snils = '') {
        $ob = new self($name, $birthdate, $inn, $snils);
        $ob->setFemale();
        return $ob;
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
     *
     */
    public function setMale() {
        $this->sexId = self::GENDER_MALE;
    }

    /**
     *
     */
    public function setFemale() {
        $this->sexId = self::GENDER_FEMALE;
    }

    /**
     * @return array
     */
    public function getPhones() {
        return [
            'mobilePhone' => empty($this->phones) ? "" : $this->phones[0],
        ];
    }

    /**
     * @return array
     */
    public function getEmails() {
        return [
            'email' => empty($this->email) ? "" : $this->email[0],
        ];
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

            if( $fld->isPublic() ) {
                $aRet[$sName] = $ob->{$sName};
            }
            else if( $fld->isPrivate() | $fld->isProtected() ) {
                $fld->setAccessible(true);
                $oPrivate = $fld->getValue($ob);
                $sGetter = 'get' . ucfirst($sName);
                if( is_object($oPrivate) ) {
                    $aRet = array_merge($aRet, $this->getDataRecurcive($oPrivate));
                }
                else if( method_exists($ob, $sGetter) ) {
                    $aRet = array_merge($aRet, $ob->{$sGetter}());
                }
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

}