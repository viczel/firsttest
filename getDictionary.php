<?php

use brainysoft\testmultibase\CliOptions;
use brainysoft\testmultibase\connector\Sender;
use brainysoft\testmultibase\connector\ConfigFactory;

require(__DIR__ . '/vendor/autoload.php');

//$customerId = 'a1';
//$customerId = 'b1';
// $customerId = 'fastmoney';

$obOpt = new CliOptions(['customer:', 'client::']);

// print_r($obOpt->getOptions());

$customerId = $obOpt->getOption('customer', 'a1');
$oConf = ConfigFactory::createConfig($customerId);

$oSender = new Sender($oConf, ["Content-Type" => "application/json",]);
$oSender->send('get', '/bs-core/dicts/credit-products');

if( !$oSender->hasError() ) {
    print_r($oSender->getData());
}
else {
    $oErr = $oSender->getError()->getResponse()->getBody();
    $data = json_decode($oErr->getContents(), true);
    echo "Error: " . print_r($data, true);
    echo "\nContext: " . print_r($oSender->oContext, true);
}
