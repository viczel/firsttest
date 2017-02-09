<?php

use brainysoft\testmultibase\CliOptions;
//use brainysoft\testmultibase\connector\Sender;
//use brainysoft\testmultibase\connector\ConfigFactory;
use brainysoft\testmultibase\connector\SenderFactory;
use brainysoft\testmultibase\connector\ApiAdapter;
use Faker\Factory;
use brainysoft\testmultibase\DataGenerator;

require(__DIR__ . '/vendor/autoload.php');

//$customerId = 'a1';
//$customerId = 'b1';
// $customerId = 'fastmoney';

$obOpt = new CliOptions(['customer:', 'client::', 'path::']);

// print_r($obOpt->getOptions());

$customerId = $obOpt->getOption('customer', 'a1');
$path = $obOpt->getOption('path', '/bs-core/dicts/credit-products');

//$oConf = ConfigFactory::createConfig($customerId);
//$oSender = new Sender($oConf, ["Content-Type" => "application/json",]);

$oSender = SenderFactory::createSender($customerId);

//$oFaker = Factory::create('ru_Ru');
//echo 'middleNameFemale: ' . $oSender->convertTo866($oFaker->lastName . ' ' . $oFaker->middleNameMale . ' ' . $oFaker->firstNameMale) . "\n";
//for($i = 0; $i < 10; $i++) {
//    echo 'name: ' . $oSender->convertTo866($oFaker->name()) . "\n";
//}
//die();

//$response = $oSender->send('get', $path);
//
//echo 'Products: ' . substr($oSender->convertTo866(print_r($response, true)), 0, 300) . "\n";
//if( !$oSender->hasError() ) {
//    $aData = $oSender->getData();
////    echo 'Products: ' . substr($oSender->convertTo866(print_r($aData->data, true)). 0, 300) . "\n";
//}
//else {
//    $oError = $oSender->getError();
//    if( method_exists($oError, 'getResponse') ) {
//        $oError = json_decode($oError->getResponse()->getBody()->getContents(), true);
//    }
//    echo 'Error: ' . $oSender->convertTo866(print_r($oError, true)) . "\n";
//}
//die();

/*

$oFaker = Factory::create('ru_Ru');
$oGenerator = new DataGenerator($oFaker);

$adapter = new ApiAdapter($oSender);

//$aProducts = $adapter->getProductList();
$aProducts = $adapter->getRawProductList();
//$oProduct = $oGenerator->getProduct($aProducts);
//$a1 = $oGenerator->createCreditForProduct($oProduct);
//
//echo 'Products: ' . $oSender->convertTo866(print_r($oProduct, true)) . "\n";
//echo 'Loan: ' . $oSender->convertTo866(print_r($a1, true)) . "\n";
//die();

echo 'Error: ' . $oSender->convertTo866(print_r($adapter->getError(), true)) . "\n";
echo 'Products: ' . $oSender->convertTo866(print_r($aProducts, true)) . "\n";

$params = [
    'products' => $aProducts, // array_keys($aProducts),
];

$oLead = $oGenerator->generateOneLead($params);
$nLeadId = $adapter->addLead($oLead);

echo $oSender->convertTo866(print_r($nLeadId, true)) . "\n";

$addedLead = $adapter->getLead($nLeadId);

echo $oSender->convertTo866(print_r($addedLead, true));


$aRes = $adapter->startLeadTest($nLeadId);
if( $adapter->hasError() ) {
    $oErr = $adapter->getErrorData();
    echo 'Error start lead test: ' . $oErr->code . ' [' . $oErr->message . '] ' . $oErr->type . "\n";
}
else {
    echo 'Start lead test: ok ' . $oSender->convertTo866(print_r($aRes, true)) . "\n";
}
//echo $oSender->convertTo866(print_r($adapter->prepareLeadList($adapter->getLeadList('2017-02-08T15:00:00', '2017-02-08T20:00:00')), true));
// print_r($adapter->getCustomerConfig());

die();
*/

$oSender->send('get', $path);

if( !$oSender->hasError() ) {
    $responseData = $oSender->getData();
    if( $responseData->status == 'ok' ) {
        $adataFiltered = filterdata($path, $responseData->data);
        echo "Context: " . print_r($oSender->oContext, true) . "\n";
        echo $oSender->convertTo866(print_r($adataFiltered, true));
    }
    else {
        print_r($responseData);
    }
}
else {
    $oErr = $oSender->getError()->getResponse()->getBody();
    $data = json_decode($oErr->getContents(), true);
    echo "Error: " . print_r($data, true);
    echo "\nContext: " . print_r($oSender->oContext, true);
}

/**
 * @param string $path
 * @param array $data
 * @return array|mixed
 */
function filterdata($path = '', $data = []) {
    if( $path == '/bs-core/dicts/credit-products' ) {
        return reduceCreditproducts($data);
    }
    else if( strpos($path, '/bs-core/main/leads/find') === 0 ) {
        return reduceLeads($data);
    }
    return $data;
}

/**
 * @param array $data
 * @return mixed
 */
function reduceCreditproducts($data) {
    return array_reduce(
        $data,
        function ($carry, $el) {
            $carry[$el->id] = $el->name;
            return $carry;
        },
        []
    );
}

function reduceLeads($data) {
    return array_reduce(
        $data,
        function ($carry, $el) {
            $t = intval($el->creationDate / 1000);
            $carry[$el->id] = $el->lastName
                . ' ' . $el->firstName
                . ' ' . $el->patronymic
                . ' ' . date('d.m.Y H:i:s', $t)
                . ' ' . $el->channel
                . ' ' . $el->mobilePhone;

            return $carry;
        },
        []
    );
}