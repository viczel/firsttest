<?php

use brainysoft\testmultibase\DataGenerator;
use Faker\Factory;

class DataGeneratorTest extends \Codeception\Test\Unit
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
    public function testLeads()
    {
        $oFaker = Factory::create('ru_Ru');

        $oFaker->seed(100);
        $sFirstName = 'Егор';
        $sLastName = 'Лапин';
        $sBithPlace = 'Одинцово';
        $sBirthDate = '1980-08-17';
        $amount = 0;
        $intRate = 0;
        $period = 0;
        $creditProductId = null;

        $params = [
//            'products' => [1, 2, 3, 4, 5, 6],
        ];

        $oGenerator = new DataGenerator($oFaker);

        $oLead = $oGenerator->generateOneLead($params);
        $data = $oLead->getLeadData();


        $this->assertEquals($sFirstName, $data['firstName'], $this->convertTo866('firstName expect to be: ' . $sFirstName . ', present: ' . $data['firstName']));
        $this->assertEquals($sLastName, $data['lastName'], $this->convertTo866('firstName expect to be: ' . $sLastName . ', present: ' . $data['lastName']));
        $this->assertEquals($sBithPlace, $data['birthPlace'], $this->convertTo866('birthPlace expect to be: ' . $sBithPlace . ', present: ' . $data['birthPlace']));
        $this->assertEquals($sBirthDate, $data['birthDate'], $this->convertTo866('birthPlace expect to be: ' . $sBirthDate . ', present: ' . $data['birthDate']));

        $this->assertEquals($amount, $data['amount'], $this->convertTo866('amount expect to be: ' . $amount . ', present: ' . $data['amount']));
        $this->assertEquals($intRate, $data['intRate'], $this->convertTo866('intRate expect to be: ' . $intRate . ', present: ' . $data['intRate']));
        $this->assertEquals($period, $data['period'], $this->convertTo866('period expect to be: ' . $period . ', present: ' . $data['period']));

        $this->assertEquals($creditProductId, $data['creditProductId'], $this->convertTo866('creditProductId expect to be: ' . $creditProductId . ', present: ' . $data['creditProductId']));

//        $numData = 5;
//        $a = $oGenerator->getLeads($numData);
//        $this->assertCount($numData, $a, 'We need ' . $numData . ' results');
//
//        $aExpect = [
//            'Назар',
//            'Данила',
//            'Клара',
//            'София',
//            'Софья',
//        ];
//
//        $this->assertEquals(
//            $aExpect,
//            $a,
//            'Expected names are not equal results: '
//            . "\nExpect: " . iconv('UTF-8', 'CP866', print_r($aExpect, true))
//            . "\nResult: " . iconv('UTF-8', 'CP866', print_r($a, true))
//        );
    }

    public function testManyLeads()
    {
        $oFaker = Factory::create('ru_Ru');

        $params = [
        ];

        $oGenerator = new DataGenerator($oFaker);
        $aLead = [];

        for($i = 0; $i < 10; $i++) {
            $aLead[$i] = $oGenerator->generateOneLead($params);
        }

        for($i = 0; $i < 9; $i++) {
            $a1 = $aLead[$i]->getLeadData();
            $a2 = $aLead[$i+1]->getLeadData();
            $this->assertNotEquals($a1['firstName'], $a2['firstName'], $this->convertTo866('Name must be not equal (' . $i . ' ' . ($i + 1) . ') ' . $a2['firstName'] . ' != ' . $a2['firstName']));
        }
    }

    /**
     * @param string $s
     * @return string
     */
    public function convertTo866($s) {
        return iconv('UTF-8', 'CP866', $s);
    }



}