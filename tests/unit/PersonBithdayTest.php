<?php

use brainysoft\testmultibase\PersonBirthday;

class PersonBithdayTest extends \Codeception\Test\Unit
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
    public function testEmptyBithday()
    {
        $oBith = new PersonBirthday();
        $this->assertEmpty($oBith->birthDate);
    }

    /**
     *
     */
    public function testErrorDate()
    {
        $this->expectException(\InvalidArgumentException::class);
        $oBith = new PersonBirthday('2014');
    }

    /**
     *
     */
    public function testErrorDateInFuture()
    {
        $this->expectException(\InvalidArgumentException::class);
        $oBith = new PersonBirthday(date('Y-m-d', time() + 24 * 3600));
    }

    /**
     *
     */
    public function testErrorMonthDate()
    {
        $this->expectException(\InvalidArgumentException::class);
        $oBith = new PersonBirthday('2000-50-40');
    }

    /**
     *
     */
    public function testOkDate()
    {
        $oBith = new PersonBirthday(date('Y-m-d', time() - 24 * 3600));
    }

}
