<?php

use brainysoft\testmultibase\DataGenerator;
use Faker\Factory;
use brainysoft\testmultibase\CliOptions;
use brainysoft\testmultibase\connector\Sender;
use brainysoft\testmultibase\connector\ConfigFactory;

require(__DIR__ . '/vendor/autoload.php');


$obOpt = new CliOptions(['customer:', 'client::']);

// print_r($obOpt->getOptions());

$customerId = $obOpt->getOption('customer', 'a1');
$oConf = ConfigFactory::createConfig($customerId);

$oFaker = Factory::create('ru_Ru');

$oGenerator = new DataGenerator($oFaker);

$params = [
    'products' => [1],
];

$oLead = $oGenerator->generateOneLead($params);
$data = $oLead->getLeadData();


$oSender = new Sender($oConf, ["Content-Type" => "application/json",]);
$oSender->send('post', '/bs-core/main/leads', $data);

if( !$oSender->hasError() ) {
    print_r($oSender->getData());
}
else {
    $oErr = $oSender->getError()->getResponse()->getBody();
    $responseData = json_decode($oErr->getContents(), true);
    echo "Error: " . print_r($responseData, true);
//    echo "Error: " . print_r($oErr->getContents(), true);
//    echo "\nContext: " . print_r($oSender->oContext, true);
//    echo "\nData: " . print_r($oSender->convertTo866(print_r($data, true)), true);
}
