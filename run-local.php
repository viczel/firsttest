<?php

require(__DIR__ . '/vendor/autoload.php');

use Faker\Factory;
use brainysoft\testmultibase\DataGenerator;


$oFaker = Factory::create('ru_Ru');
$oFaker->seed(100);
$numData = 5;

$oGenerator = new DataGenerator($oFaker);

$a = $oGenerator->getLeads($numData);

echo iconv('UTF-8', 'CP866', print_r($a, true));
