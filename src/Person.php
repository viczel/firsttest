<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 06.02.2017
 * Time: 14:20
 */

namespace brainysoft\testmultibase;

use brainysoft\testmultibase\BaseLead;
use brainysoft\testmultibase\PersonName;
use brainysoft\testmultibase\PersonBirthday;
use brainysoft\testmultibase\CreditCard;
use brainysoft\testmultibase\PersonAddress;
use brainysoft\testmultibase\PersonPassport;
use brainysoft\testmultibase\Employer;

class Person extends BaseLead
{
    const GENDER_MALE = 101251;    // Мужчина
    const GENDER_FEMALE = 101252;  // Женщина


    public $sexId = self::GENDER_MALE; // Пол

    public $inn;   // ИНН.
    public $snils; // СНИЛС.

    public $childrenCount = 0;      // Количество детей.
    public $adultChildrenCount = 0; // Количество совершенно летних детей.
    public $dependentsCount = 0;    // Количество иждивенцев.

    protected $name = null;       // Имя
    protected $birthdate = null;  // день рождения

    protected $passport = null;   // паспорт

    protected $creditcard = null;   // кредитная карта

    protected $addressData = [];   // Адрес проживания
    protected $registrationAddressData = [];   // Адрес прописки

    protected $employer = null;

    public function __construct(PersonName $name, PersonBirthday $birthdate, $inn = '', $snils = '')
    {
        $this->name = $name;
        $this->birthdate = $birthdate;

        $this->naturalPerson = true;

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
        $ob = new Person($name, $birthdate, $inn, $snils);
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
        $ob = new Person($name, $birthdate, $inn, $snils);
        $ob->setFemale();
        return $ob;
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
     * @param \brainysoft\testmultibase\CreditCard $card
     */
    public function setCreditcard(CreditCard $card) {
        $this->creditcard = $card;
    }

    /**
     * @return array
     */
    public function getCreditcard() {
        if( empty($this->creditcard) ) {
            return [];
        }
        return $this->getDataRecurcive($this->creditcard);
    }

    /**
     * @param \brainysoft\testmultibase\PersonAddress $address
     */
    public function setAddressData(PersonAddress $address) {
        $this->addressData = $address;
    }

    /**
     * @return array
     */
    public function getAddressData() {
        return [
            'addressData' => empty($this->addressData) ? $this->getDataRecurcive(new PersonAddress()) : $this->getDataRecurcive($this->addressData),
        ];
    }

    /**
     * @param \brainysoft\testmultibase\PersonAddress $address
     */
    public function setRegistrationAddressData(PersonAddress $address) {
        $this->registrationAddressData = $address;
    }

    /**
     * @return array
     */
    public function getRegistrationAddressData() {
        return [
            'registrationAddressData' => empty($this->registrationAddressData) ? $this->getDataRecurcive(new PersonAddress()) : $this->getDataRecurcive($this->registrationAddressData),
        ];
    }

    /**
     * @param \brainysoft\testmultibase\PersonPassport $passport
     */
    public function setPassport(PersonPassport $passport) {
        $this->passport = $passport;
    }

    /**
     * @return array
     */
    public function getPassport() {
        return [
            'passport' => empty($this->passport) ? null : $this->getDataRecurcive($this->passport),
        ];
    }

    /**
     * @param \brainysoft\testmultibase\Employer $employer
     */
    public function setEmployer(Employer $employer) {
        $this->employer = $employer;
    }

    /**
     * @return array
     */
    public function getEmployer() {
        return $this->getDataRecurcive(empty($this->employer) ? new Employer() : $this->employer);
    }

}