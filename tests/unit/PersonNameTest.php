<?php

use brainysoft\testmultibase\PersonName;

class PersonNameTest extends \Codeception\Test\Unit
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
    public function testNameSetFields()
    {
        $sLast = 'Ivanov';
        $sFirst = 'Petr';
        $ob = new PersonName($sFirst, $sLast);
        $this->assertEquals($ob->lastName, $sLast);
        $this->assertEquals($ob->firstName, $sFirst);
        $this->assertEquals($ob->getFullName(), "{$sLast} {$sFirst}");
    }

    public function testFullName() {
        $sLast = 'Ivanov';
        $sFirst = 'Petr';
        $sOtch = 'Nicolaevich';
        $ob = new PersonName($sFirst, $sLast, $sOtch);
        $this->assertEquals($ob->getFullName(), "{$sLast} {$sFirst} {$sOtch}");
    }
}