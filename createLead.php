<?php

use brainysoft\testmultibase\CliOptions;
use brainysoft\testmultibase\connector\BatchSender;
use brainysoft\testmultibase\connector\ApiAdapter;
use brainysoft\testmultibase\connector\ConfigFactory;
use brainysoft\testmultibase\connector\SenderFactory;
use brainysoft\testmultibase\connector\Config;
use Faker\Factory;
use brainysoft\testmultibase\DataGenerator;
use brainysoft\testmultibase\Person;

require(__DIR__ . '/vendor/autoload.php');

$obOpt = new CliOptions(['customer:', 'count::']);

$customer = $obOpt->getOption('customer', '');
$countData = $obOpt->getOption('count', 2);

if( empty($customer) ) {
    die('Command format: php ' . basename(__FILE__) . " --customer=b1,a2,b3 [--count=2]");
}

$customerList = explode(',', $customer);

//echo "customer: " . print_r($customerList, true) . "\n";

$configList = getConfigList($customerList);
$adapterList = getAdapterList($customerList);
$aproductList = getProductList($adapterList);

//echo print_r($configList, true);

$oFaker = Factory::create('ru_Ru');
$oGenerator = new DataGenerator($oFaker);

$aHeaders = ["Content-Type" => "application/json",];
$oBatchSender = new BatchSender(new \GuzzleHttp\Client(), $aHeaders);

//echo 'Error: ' . $oSender->convertTo866(print_r($adapter->getError(), true)) . "\n";
//echo 'Products: ' . $oSender->convertTo866(print_r($aProducts, true)) . "\n";

foreach($customerList As $customerId) {
    $aGetMoneyMethods = $adapterList[$customerId]->getGettingMonewMethods();
    $params = [
        'products' => $aproductList[$customerId], // array_keys($aProducts),
        'gettingmoneymethod' => $aGetMoneyMethods,
        'customerid' => $customerId,
    ];
    $al = [];
    for($i = 0; $i < $countData; $i++) {
//        $params['key'] = $customerId . '-' . $i;
        $oLead = $oGenerator->generateOneLead($params);
        $data = $oLead->getLeadData();
        addAppendRequest($oBatchSender, $configList[$customerId], $data);
    }
}
//die();
$oBatchSender->send();

for($i = 0; $i < $oBatchSender->getCount(); $i++) {
    $aResult = $oBatchSender->getResult($i);


    if( $aResult['error'] === null ) {
        $a = json_decode($aResult['response']);
        $a->customer = $aResult['customer'];
//        echo print_r($a, true);
        $s = $aResult['customer'] . ' : add lead ' . $a->data . "\n";
    }
    else {
        echo print_r([$aResult['customer'], $aResult['error']], true);
        $s = $aResult['customer'] . ' : error ' . print_r($aResult['error'], true) . "\n";
    }
    echo $s;

}
/*
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
    sleep(10);
    $aTestStatuses = $adapter->getTestStatuses($nLeadId);
//    echo 'Status : ' . $oSender->convertTo866(print_r($aTestStatuses, true)) . "\n";
    $oStatus = $adapter->getLastTestStatus($aTestStatuses);
    if( $oStatus->status != 'APPROVED' ) {
        echo 'Test status: ' . $oSender->convertTo866(print_r($oStatus, true)) . "\n";
    }
    else {
        echo "Test status: ok\n";
    }
}
*/


/**
 *
 * @param array $customerList
 * @return array
 *
 */
function getConfigList($customerList = []) {
    return array_reduce(
        $customerList,
        function($carry, $el) {
            echo "el: " . $el . "\n";
            $carry[$el] = ConfigFactory::createConfig($el);
            return $carry;
        },
        []
    );
}

/**
 *
 * @param array $customerList
 * @return array
 *
 */
function getAdapterList($customerList = []) {
    return array_reduce(
        $customerList,
        function($carry, $el) {
//            echo "el: " . $el . "\n";
            $carry[$el] = new ApiAdapter(SenderFactory::createSender($el));
            return $carry;
        },
        []
    );
}

/**
 *
 * @param array $customerList
 * @return array
 *
 */
function getProductList($adapterList) {
    $aProducts = [];
    foreach ($adapterList As $k=>$v) {
        $aProducts[$k] = $adapterList[$k]->getRawProductList();
    }
    return $aProducts;
}

/**
 * @param BatchSender $oSender
 * @param Person $lead
 */
function addAppendRequest(BatchSender $oSender, Config $config, $data /*Person $lead*/) {
//    $data = $lead->getLeadData();
    $s = $config->customer . ': ' . $data['lastName'] . ' ' . $data['firstName'] . ' ' . $data['email'] . ' ' . $data['mobilePhone'] . "\n";
//    echo iconv('UTF-8', 'CP866', $s);
    $oSender->addRequest($config, 'POST', '/bs-core/main/leads', $data);
}

