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

    }
}