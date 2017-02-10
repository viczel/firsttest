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

$obOpt = new CliOptions(['customer:', 'start::', 'finish::']);

$customer = $obOpt->getOption('customer', '');
$startdate = $obOpt->getOption('start', date('Y-m-d') . 'T00:00:00');
$finishdate = $obOpt->getOption('finish', date('Y-m-d') . 'T23:59:59');

if( empty($customer) ) {
    die('Command format: php ' . basename(__FILE__) . " --customer=b1,a2,b3 [--start=2017-02-01T00:00:00] [--finish=2017-02-05T00:00:00]");
}

$customerList = explode(',', $customer);

//echo "customer: " . print_r($customerList, true) . "\n";

$configList = getConfigList($customerList);
$adapterList = getAdapterList($customerList);
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
        addFindRequest($oBatchSender, $configList[$customerId], $startdate, $finishdate);
//    }
}

$oBatchSender->send();

$aNewData = [];
for($i = 0; $i < $oBatchSender->getCount(); $i++) {
    $aResult = $oBatchSender->getResult($i);
//    print_r($aResult);
//    die();

    if( $aResult['error'] === null ) {
        $customer = $aResult['customer'];
        $a = json_decode($aResult['response']);
        foreach ($a->data As $ob) {
            if( !isset($aNewData[$customer]) ) {
                $aNewData[$customer] = [];
            }
            $aNewData[$customer][] = $ob->id;
//            break;
        }
        $s = '';
    }
    else {
//        echo print_r([$aResult['customer'], $aResult['error']], true);
        $s = $aResult['customer'] . ' : error ' . print_r($aResult['error'], true) . "\n";
    }
    echo iconv('UTF-8', 'CP866', $s);
}

foreach ($aNewData As $k => $v) {
    echo $k . ' : ' . count($v) . "\n";
}
$oBatchSender->clearRequests();

//echo iconv('UTF-8', 'CP866', print_r($aNewData, true));
//
//die();

//$oFaker = Factory::create('ru_Ru');

foreach ($aNewData As $k =>$arr) {
    foreach($arr As $id) {
        $ob = $adapterList[$k]->getLead($id);
//        $ob->firstName = $oFaker->firstNameMale;
//        $ob->lastName = $oFaker->lastName;
        addCheckRequest($oBatchSender, $configList[$k], $ob);
    }
}

$oBatchSender->send();
$aUpdate = [];

for($i = 0; $i < $oBatchSender->getCount(); $i++) {
    $aResult = $oBatchSender->getResult($i);
    $customer = $aResult['customer'];
    if( !isset($aUpdate[$customer]) ) {
        $aUpdate[$customer] = [
            'ok' => [],
            'error' => [],
        ];
    }
//    print_r($aResult);
//    die();

    if( empty($aResult['error']) ) {
        $customer = $aResult['customer'];
        $a = json_decode($aResult['response']);
//        $s = print_r($a, true);
        $s = '';
        if( $a->status == 'ok' ) {
            $aUpdate[$customer]['ok'][] = $a->data;
        }
        else {
            $aUpdate[$customer]['error'][] = $a->data;
        }
        $s = '';
    }
    else {
//        echo print_r([$aResult['customer'], $aResult['error']], true);
        $s1 = $aResult['error']->getResponse()->getBody()->getContents();
//        $s = $aResult['customer'] . ' : error ' . print_r($aResult['error'], true) . "\n";
        $s = $aResult['customer'] . ' : error ' . $s1 . "\n";

        $aUpdate[$customer]['error'][] = json_decode($s1);
    }

//    echo substr(iconv('UTF-8', 'CP866', $s), 0, 500) . "\n";
//    break;
}

foreach ($aUpdate As $k => $a) {
    echo $k . ' : ok: ' . count($a['ok']) . ' err: ' . count($a['error']) . "\n";

    // при повторной проверке возникает UNEXPECTED_LEAD_STATUS_VALUE

    if( count($a['error']) > 0 ) {
        echo $k . ' error : ' . print_r($a['error'][0], true) . "\n";
    }
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
function addFindRequest(BatchSender $oSender, Config $config, $start='', $finish='') {
    $oSender->addRequest($config, 'get', '/bs-core/main/leads/find/date-from/'.$start.'/date-to/' . $finish . '?lead-status=WAITING');
}

/**
 * @param BatchSender $oSender
 * @param Config $config
 * @param string $start
 * @param string $finish
 */
function addCheckRequest(BatchSender $oSender, Config $config, $ob) {
    $oSender->addRequest($config, 'post', '/bs-core/main/leads/'. $ob->id . '/check');
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
