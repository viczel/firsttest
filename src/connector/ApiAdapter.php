<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 09.02.2017
 * Time: 12:09
 */

namespace brainysoft\testmultibase\connector;

use brainysoft\testmultibase\connector\Sender;
use brainysoft\testmultibase\Person;

class ApiAdapter
{
    public $sender = null;
    public $error = null;
    public $response = null;

    public static $_cache = [];

    public function __construct(Sender $sender)
    {
        $this->sender = $sender;
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $data
     * @param array $headers
     * @return array
     */
    public function execute($method = '', $path = '', $data = [], $headers = []) {
        $this->error = null;
        $aReturn = [];

        $this->sender->send($method, $path, $data, $headers);

        if( $this->sender->hasError() ) {
            $this->error = $this->sender->getError();
        }
        else {
            $aReturn = $this->sender->getData()->data;
        }

        return $aReturn;
    }

    /**
     * @return bool
     */
    public function hasError() {
        return ($this->error !== null);
    }

    /**
     * @return null
     */
    public function getError() {
        return $this->error;
    }

    /**
     * @return null
     */
    public function getErrorData() {
        if(
            ($this->error !== null)
            && property_exists($this->error, 'status')
            && ($this->error->status == 'error')
        ) {
            return $this->error->data;
        }
        return null;
    }

    /**
     * @return array
     */
    public function getRawProductList() {
        $sCacheKey = 'get-/bs-core/dicts/credit-products';
        if( key_exists($sCacheKey, self::$_cache) ) {
            return self::$_cache[$sCacheKey];
        }
        $this->execute('get', '/bs-core/dicts/credit-products');
        $aReturn = [];
        if( !$this->hasError() ) {
            $aReturn = $this->sender->getData()->data;
            self::$_cache[$sCacheKey] = $aReturn;
        }
        return $aReturn;
    }

    /**
     * @return array
     */
    public function getProductList() {
        $aReturn = $this->prepareProductList($this->getRawProductList());
        return $aReturn;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function prepareProductList($data = []) {
        return array_reduce(
            $data,
            function ($carry, $el) {
                $carry[$el->id] = $el->name;
                return $carry;
            },
            []
        );
    }

    /**
     * @return array
     */
    public function getVersionInfo() {
        $aReturn = $this->execute('get', '/bs-core/utils/version-info');
        return $aReturn;
    }

    /**
     * @return array
     */
    public function getCustomerConfig() {
        $aReturn = $this->execute('get', '/bs-core/dicts/custom/config');
        return $aReturn;
    }

    /**
     * @param string $from
     * @param string $to
     * @return array
     */
    public function getLeadList($from = '', $to = '') {
        $tz = new \DateTimeZone('Europe/London');
        $tServer = new \DateTime('now', $tz);
        $tStart = \DateTime::createFromFormat('Y-m-d', $tServer->format('Y-m-d'), $tz);
        if( empty($from) ) {
            $from = substr($tStart->format('c'), 0, -6);
        }
        if( empty($to) ) {
            $to = substr($tServer->format('c'), 0, -6);
        }
        $sPath = '/bs-core/main/leads/find/date-from/'.$from.'/date-to/' . $to;
//        echo $sPath . "\n";
        $aReturn = $this->execute('get', $sPath);
        return $aReturn;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function prepareLeadList($data = []) {
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

    /**
     * @param Person $lead
     * @return array
     */
    public function addLead(Person $lead) {
        $data = $lead->getLeadData();
        $result = $this->execute('post', '/bs-core/main/leads', $data);
        return $result;
    }

    /**
     * @param Person $lead
     * @return array
     */
    public function getLead($leadId) {
        $result = $this->execute('get', '/bs-core/main/leads/' . intval($leadId));
        return $result;
    }

    /**
     * @param Person $lead
     * @return array
     */
    public function startLeadTest($leadId) {
        $result = $this->execute('post', '/bs-core/main/leads/' . intval($leadId) . '/check');
        return $result;
    }

    /**
     * @param int $leadId
     * @return array
     */
    public function getTestStatuses($leadId = 0) {
        $result = $this->execute('get', '/bs-core/main/leads/' . intval($leadId) . '/statuses');
        return $result;
    }

    /**
     * @param int $id
     * @return array
     */
    public function getContract($id) {
        $result = $this->execute('get', '/bs-core/main/contracts/' . intval($id));
        return $result;
    }

    /**
     * @param int $id
     * @return array
     */
    public function getClient($id) {
        $result = $this->execute('get', '/bs-core/main/clients/' . intval($id));
        return $result;
    }

}