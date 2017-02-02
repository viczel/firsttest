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
        $numData = 5;

        $oGenerator = new DataGenerator($oFaker);

        $a = $oGenerator->getLeads($numData);
        $this->assertCount($numData, $a, 'We need ' . $numData . ' results');

        $aExpect = [
            'Назар',
            'Данила',
            'Клара',
            'София',
            'Софья',
        ];

        $this->assertEquals(
            $aExpect,
            $a,
            'Expected names are not equal results: '
            . "\nExpect: " . iconv('UTF-8', 'CP866', print_r($aExpect, true))
            . "\nResult: " . iconv('UTF-8', 'CP866', print_r($a, true))
        );
    }
}