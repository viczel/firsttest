<?php

use brainysoft\testmultibase\Person;
use brainysoft\testmultibase\PersonName;
use brainysoft\testmultibase\PersonBirthday;

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

    // tests
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
            'mobilePhone',
            'email',
        ];

        $aFields = $oPerson->getLeadData();
        $aDiff = array_merge(
            array_diff($aNeedFields, array_keys($aFields)),
            array_diff(array_keys($aFields), $aNeedFields)
        );

        $this->assertCount(0, $aDiff);

        $this->assertEquals($aFields['firstName'], $sFirstName);
        $this->assertEquals($aFields['lastName'], $sLastName);

        $this->assertNull($aFields['birthDate']);

        $this->assertEquals('', $aFields['inn']);
        $this->assertEquals('', $aFields['snils']);
        $this->assertEquals('', $aFields['patronymic']);
        $this->assertEquals('', $aFields['birthPlace']);

        $this->assertEquals(Person::GENDER_MALE, $aFields['sexId']);
    }

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


}