<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 06.02.2017
 * Time: 12:32
 */

namespace brainysoft\testmultibase;


class PersonAddress
{
    public $countryId;
    public $fullAddressText;
    public $fiasChainText;
    public $houseNo;
    public $blockNo;
    public $buildingNo;
    public $apartmentNo;
    public $telephone;
    public $postalCode;
    public $regDate;
    public $metroStation;
    public $fiasId;
    public $regionName;
    public $localityName;
    public $streetName;

    public function __construct(
        $countryId = null,
        $fullAddressText = null,
        $fiasChainText = null,
        $houseNo = null,
        $blockNo = null,
        $buildingNo = null,
        $apartmentNo = null,
        $telephone = null,
        $postalCode = null,
        $regDate = null,
        $metroStation = null,
        $fiasId = null,
        $regionName = null,
        $localityName = null,
        $streetName = null)
    {
        $this->countryId = $countryId;
        $this->fullAddressText = $fullAddressText;
        $this->fiasChainText = $fiasChainText;
        $this->houseNo = $houseNo;
        $this->blockNo = $blockNo;
        $this->buildingNo = $buildingNo;
        $this->apartmentNo = $apartmentNo;
        $this->telephone = $telephone;
        $this->postalCode = $postalCode;
        $this->regDate = $regDate;
        $this->metroStation = $metroStation;
        $this->fiasId = $fiasId;
        $this->regionName = $regionName;
        $this->localityName = $localityName;
        $this->streetName = $streetName;
    }

}