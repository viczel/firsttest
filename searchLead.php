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

$obOpt = new CliOptions(['customer:', '', ]); // 'start::', 'finish::'

$customer = $obOpt->getOption('customer', '');
//$startdate = $obOpt->getOption('start', date('Y-m-d') . 'T00:00:00');
//$finishdate = $obOpt->getOption('finish', date('Y-m-d') . 'T23:59:59');

if( empty($customer) ) {
    die('Command format: php ' . basename(__FILE__) . " --customer=b1,a2,b3");
}

$customerList = explode(',', $customer);

//echo "customer: " . print_r($customerList, true) . "\n";

$configList = getConfigList($customerList);
//$adapterList = getAdapterList($customerList);
//$aproductList = getProductList($adapterList);

//echo print_r($configList, true);

//$oFaker = Factory::create('ru_Ru');
//$oGenerator = new DataGenerator($oFaker);

$aHeaders = ["Content-Type" => "application/json",];
$oBatchSender = new BatchSender(new \GuzzleHttp\Client(), $aHeaders);

//echo 'Error: ' . $oSender->convertTo866(print_r($adapter->getError(), true)) . "\n";
//echo 'Products: ' . $oSender->convertTo866(print_r($aProducts, true)) . "\n";

foreach($customerList As $customerId) {
//    $aGetMoneyMethods = $adapterList[$customerId]->getGettingMonewMethods();
//    $params = [
//        'products' => $aproductList[$customerId], // array_keys($aProducts),
//        'gettingmoneymethod' => $aGetMoneyMethods,
//        'customerid' => $customerId,
//    ];
//    for($i = 0; $i < $countData; $i++) {
//        $oLead = $oGenerator->generateOneLead($params);
    addSearchRequest($oBatchSender, $configList[$customerId], ['email' => $customerId . '.%', 'firstName' => '%ий'], 0, 20);
//    addSearchRequest($oBatchSender, $configList[$customerId], ['email' => $customerId . '.%', 'lastName' => '%ир%'], 0, 20);
//    addSearchRequest($oBatchSender, $configList[$customerId], ['email' => $customerId . '.%'], 0, 20);
//    }
}

$oBatchSender->send();

for($i = 0; $i < $oBatchSender->getCount(); $i++) {
    $aResult = $oBatchSender->getResult($i);
    if( $aResult['error'] === null ) {

        $customer = $aResult['customer'];
        $a = json_decode($aResult['response']);
//        print_r($a);
//        continue;
        $aLeads = filterLeads($a->data);
        $aMistakes = array_filter($aLeads, function ($el) use ($customer) { return !empty($customer) && ($customer != $el['customer']); });
        $sMistake = array_reduce(
            $aMistakes,
            function($carry, $el) {
                $carry .= (is_object($el) ? print_r($el, true) : implode(', ', $el)) . "\n";
                return $carry;
            },
            ''
        );
        $s = $aResult['customer'] . ' ' . $aResult['request']['url'] . ' : leads ' . count($a->data) . '/' . count($aMistakes) . ' ' . $sMistake . "\n";
    }
    else {
//        echo print_r([$aResult['customer'], $aResult['error']], true);
        $s1 = $aResult['error']->getResponse()->getBody()->getContents();
        $s = $aResult['customer'] . ' : error ' . print_r(json_decode($s1), true) . "\n";
    }
    echo iconv('UTF-8', 'CP866', $s);
//    break;

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
//function getAdapterList($customerList = []) {
//    return array_reduce(
//        $customerList,
//        function($carry, $el) {
////            echo "el: " . $el . "\n";
//            $carry[$el] = new ApiAdapter(SenderFactory::createSender($el));
//            return $carry;
//        },
//        []
//    );
//}

/**
 *
 * @param array $customerList
 * @return array
 *
 */
//function getProductList($adapterList) {
//    $aProducts = [];
//    foreach ($adapterList As $k=>$v) {
//        $aProducts[$k] = $adapterList[$k]->getRawProductList();
//    }
//    return $aProducts;
//}

/**
 * @param BatchSender $oSender
 * @param Config $config
 * @param string $start
 * @param string $finish
 */
function addReadRequest(BatchSender $oSender, Config $config, $start='', $finish='') {
    $oSender->addRequest($config, 'get', '/bs-core/main/leads/find/date-from/'.$start.'/date-to/' . $finish);
}

/**
 * @param BatchSender $oSender
 * @param Config $config
 * @param string $start
 * @param string $finish
 */
function addSearchRequest(BatchSender $oSender, Config $config, $aFilter, $from = 0, $to = 10) {
    $aStr = [];
    foreach ($aFilter As $k => $v) {
        $aStr[] = $k . ':' . (is_string($v) ? "\"{$v}\"" : $v);
    }

    $s = '';
    if( count($aStr) > 0 ) {
        $s = '?fields-params=' . rawurlencode(implode('/', $aStr));
    }
    // если не указать fields-params= после вопросика, то сервер вылетает с ошибкой

    $sPath = '/bs-core/leads/search/count-from/'.$from.'/count-to/' . $to . $s;
//    echo $config->customer . ': ' . $sPath . "\n";
    $oSender->addRequest($config, 'get', $sPath);
}


/**
 *
 *
 * @param array $aLeads
 * @return array
 *
 */
function filterLeads($aLeads = []) {
    $aReturn = [];
    foreach ($aLeads As $ob) {
        if( !property_exists($ob, 'email') ) {
            $aReturn[] = array_merge(json_decode(json_encode($ob), true), ['customer' => '']);
        }
        else {
            $aReturn[] = [
                'id' => $ob->id,
                'date' => date('Y-m-d H:i:s', $ob->creationDate / 1000),
                'name' => $ob->lastName . ' ' . $ob->firstName . ' / ' . $ob->email,
                'phone' => $ob->mobilePhone,
                'customer' => !empty($ob->email) ? substr($ob->email, 0, strpos($ob->email, '.')) : '',
            ];
        }
    }
    return $aReturn;
}
