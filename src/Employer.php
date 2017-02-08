<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 08.02.2017
 * Time: 10:57
 */

namespace brainysoft\testmultibase;


class Employer
{
    public $employerTitle = '';
    public $employerInn = '';

    public function __construct($employerTitle = '', $employerInn = '')
    {
        $this->employerTitle = $employerTitle;
        $this->employerInn = $employerInn;
    }

}