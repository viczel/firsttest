<?php

use brainysoft\testmultibase\LeadCreator;

class LeadCreatorTest extends \Codeception\Test\Unit
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
    public function testSendOneRequest()
    {
        $ob = new LeadCreator('http://172.16.1.88:8082');
    }
}