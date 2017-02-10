<?php

use brainysoft\testmultibase\PersonSnils;

class PersonSnilsTest extends \Codeception\Test\Unit
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
    public function testCorrectSnilsNum()
    {
        $sSnils = '112-233-445 95';
        $oSnils = new PersonSnils();
        $this->assertEquals(true, $oSnils->testSnils($sSnils), 'snils need to be correct');

    }


    public function testCorrectSnilsCode()
    {
        $sSnils = '112-233-445';
        $oSnils = new PersonSnils();
        $this->assertEquals(95, $oSnils->calculateCode($sSnils), 'snils need to has correct calculation summ');

    }

    public function testCorrectSnilsCode1()
    {
        $sSnils = '069-480-319'; //  94
        $oSnils = new PersonSnils();
        $this->assertEquals(94, $oSnils->calculateCode($sSnils), 'snils need to has correct calculation summ 1');
    }

    public function testCorrectSnilsGenerator()
    {
        $oSnils = new PersonSnils();
        $sSnils = $oSnils->generateSnils();
        $this->assertEquals(true, $oSnils->testSnils($sSnils), 'generated snils ' . $sSnils . ' need to be correct');
    }



}