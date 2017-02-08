<?php

use brainysoft\testmultibase\Person;
use brainysoft\testmultibase\PersonName;
use brainysoft\testmultibase\PersonBirthday;
use brainysoft\testmultibase\PersonAddress;
use brainysoft\testmultibase\Employer;
use brainysoft\testmultibase\Credit;

class PersonTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    /**
     *
     */
    public function testMinDataForPerson()
    {
        $sFirstName = 'Petr';
        $sLastName = 'Ivanov';
        $oPerson = new Person(
            new PersonName($sFirstName, $sLastName),
            new PersonBirthday()
        );

        $this->assertEquals($oPerson->sexId, Person::GENDER_MALE);

        $aNeedFields = [
            'sexId',
            'inn',
            'snils',
            'firstName',
            'lastName',
            'patronymic',
            'birthDate',
            'birthPlace',
            'childrenCount',
            'adultChildrenCount',
            'dependentsCount',
            'addressData',
            'registrationAddressData',
            'passport',
            'employerTitle',
            'employerInn',


            'channel',
            'naturalPerson',
            'addresses',
            'managerId',
            'creditProductId',
            'extraFields',
            'mobilePhone',
            'email',
            'ipAddress',
            'macAddress',
            'ipAndRegionMatch',
            'ipAndRegAddressMatch',
            'mobilePhoneCheck',
            'rosfinmonitoringCheck',
            'ufmsCheck',
            'approvedByScorista',
            'orderCode',
            'storeCode',
            'storeTypeId',
            'referralLink',
            'deviceTypeId',
            'denialReasonId',
            'relatives',
            'gettingMoneyMethodId',
            'amount',
            'intRate',
            'period',
            'periodUnit',

            'totalAmount',
            'totalAmountDelinq30',
            'meanIncome',
            'averageMonthlyCost',
            'monthlyCreditPayment',
            'closedCreditsCount',
            'delinquencyCount',
            'payedDelinquencyCount',
            'writtenDelinquencyCount',
            'activeCreditsCount',
            'activeCreditsAmount',
            'activeDelinquencyAmount',


            'currentStatus',

            'getter',
        ];

        $aFields = $oPerson->getLeadData();
        $aKeys = array_keys($aFields);
        $aDiff = array_merge(
            array_diff($aNeedFields, $aKeys),
            array_diff($aKeys, $aNeedFields)
        );

        $this->assertCount(0, $aDiff, 'Extra fields: ' . implode(', ', $aDiff) . "\n" . print_r($aFields, true));

        $this->assertEquals($aFields['firstName'], $sFirstName);
        $this->assertEquals($aFields['lastName'], $sLastName);

        $this->assertNull($aFields['birthDate']);

        $this->assertEquals('', $aFields['inn']);
        $this->assertEquals('', $aFields['snils']);
        $this->assertEquals('', $aFields['patronymic']);
        $this->assertEquals('', $aFields['birthPlace']);

        $this->assertEquals(Person::GENDER_MALE, $aFields['sexId']);
    }

    /**
     *
     */
    public function testCreateMale()
    {
        $sFirstName = 'Petr';
        $sLastName = 'Ivanov';
        $oPerson = Person::createMale(
            new PersonName($sFirstName, $sLastName),
            new PersonBirthday()
        );

        $this->assertEquals($oPerson->sexId, Person::GENDER_MALE);
    }

    /**
     *
     */
    public function testCreateFemale()
    {
        $sFirstName = 'Petr';
        $sLastName = 'Ivanov';
        $oPerson = Person::createFemale(
            new PersonName($sFirstName, $sLastName),
            new PersonBirthday()
        );

        $this->assertEquals($oPerson->sexId, Person::GENDER_FEMALE);
    }

    /**
     *
     */
    public function testAddMobilePhone()
    {
        $sFirstName = 'Petr';
        $sLastName = 'Ivanov';
        $oPerson = Person::createFemale(
            new PersonName($sFirstName, $sLastName),
            new PersonBirthday()
        );

        $basePhone = '9031112233';
        $oPerson->addPhone($basePhone);
        $oPerson->addPhone('9015556677');

        $aPhones = $oPerson->getAllPhones();
        $this->assertCount(2, $aPhones, 'Phones must has 2 elements. Current elements:' . implode(', ', $aPhones));


        $aFields = $oPerson->getLeadData();

        $this->assertArrayHasKey('mobilePhone', $aFields, 'Array need key "mobilePhone" ' . print_r($aFields, true));
        $this->assertEquals($basePhone, $aFields['mobilePhone'], 'Mobile phone need to be ' . $basePhone);

    }

    /**
     *
     */
    public function testAddLeadEmail()
    {
        $sFirstName = 'Petr';
        $sLastName = 'Ivanov';
        $oPerson = Person::createFemale(
            new PersonName($sFirstName, $sLastName),
            new PersonBirthday()
        );

        $baseEmail = 'test@example.com';
        $oPerson->addEmail($baseEmail);
        $oPerson->addEmail('test@mfsa.ru');

        $aEmails = $oPerson->getAllEmails();
        $this->assertCount(2, $aEmails, 'Emails must has 2 elements. Current elements:' . implode(', ', $aEmails));

        $aFields = $oPerson->getLeadData();

        $this->assertArrayHasKey('email', $aFields, 'Array need key "email" ' . print_r($aFields, true));
        $this->assertEquals($baseEmail, $aFields['email'], 'Email need to be ' . $baseEmail . ' current: ' . $aFields['email']);
    }

    /**
     *
     */
    public function testAddAddress()
    {
        $sFirstName = 'Petr';
        $sLastName = 'Ivanov';
        $oPerson = Person::createFemale(
            new PersonName($sFirstName, $sLastName),
            new PersonBirthday()
        );

        $fiasId = 150;
        $sTextAddr = 'sity Moscow, street First';
        $oPerson->setAddressData(new PersonAddress($sTextAddr, $fiasId));

        $aFields = $oPerson->getLeadData();

        $this->assertArrayHasKey('addressData', $aFields, 'Array need key "addressData" ' . print_r($aFields, true));

        $this->assertEquals($sTextAddr, $aFields['addressData']['fullAddressText'], 'fullAddressText need to be ' . $sTextAddr);
        $this->assertEquals($fiasId, $aFields['addressData']['fiasId'], 'fiasId need to be ' . $fiasId);
    }

    /**
     *
     */
    public function testRegistrationAddressData()
    {
        $sFirstName = 'Petr';
        $sLastName = 'Ivanov';
        $oPerson = Person::createFemale(
            new PersonName($sFirstName, $sLastName),
            new PersonBirthday()
        );

        $fiasId = 150;
        $sTextAddr = 'sity Moscow, street First';
        $oPerson->setRegistrationAddressData(new PersonAddress($sTextAddr, $fiasId));

        $aFields = $oPerson->getLeadData();

        $this->assertArrayHasKey('registrationAddressData', $aFields, 'Array need key "registrationAddressData" ' . print_r($aFields, true));

        $this->assertEquals($sTextAddr, $aFields['registrationAddressData']['fullAddressText'], 'fullAddressText need to be ' . $sTextAddr);
        $this->assertEquals($fiasId, $aFields['registrationAddressData']['fiasId'], 'fiasId need to be ' . $fiasId);
    }

    /**
     *
     */
    public function testAddEmployer()
    {
        $sFirstName = 'Petr';
        $sLastName = 'Ivanov';
        $oPerson = Person::createFemale(
            new PersonName($sFirstName, $sLastName),
            new PersonBirthday()
        );

        $sEmplTitle = 'Employertest';
        $sEmplInn = 1234567891;

        $oPerson->setEmployer(new Employer($sEmplTitle, $sEmplInn));
        $aFields = $oPerson->getLeadData();

        $this->assertEquals($sEmplTitle, $aFields['employerTitle'], 'Employer Title: ' . $sEmplTitle . ' != ' . $aFields['employerTitle']);
        $this->assertEquals($sEmplInn, $aFields['employerInn'], 'Employer Inn: ' . $sEmplTitle . ' != ' . $aFields['employerInn']);
    }

    /**
     *
     */
    public function testSetCredit()
    {
        $sFirstName = 'Petr';
        $sLastName = 'Ivanov';
        $oPerson = Person::createFemale(
            new PersonName($sFirstName, $sLastName),
            new PersonBirthday()
        );

        $sum = 10000;
        $procent = 10;
        $period = 3;
        $unit = Credit::CREDIT_PERIOD_WEEKS;

        $oPerson->setCredit(new Credit($sum, $procent, $period, $unit));

        $aFields = $oPerson->getLeadData();

        $this->assertEquals($sum, $aFields['amount'], 'Credit summ: ' . $sum . ' != ' . $aFields['amount']);
        $this->assertEquals($procent, $aFields['intRate'], 'Credit rate: ' . $procent . ' != ' . $aFields['intRate']);
        $this->assertEquals($period, $aFields['period'], 'Credit period: ' . $period . ' != ' . $aFields['period']);
        $this->assertEquals($unit, $aFields['periodUnit'], 'Credit period unit: ' . $unit . ' != ' . $aFields['periodUnit']);
    }
}