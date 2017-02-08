<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 08.02.2017
 * Time: 16:49
 */

namespace brainysoft\testmultibase;


class CliOptions
{
    public $options = [];

    public function __construct($longOpts = [], $shortOpts = '')
    {
/*
        $shortopts  = "";
        $shortopts .= "f:";  // Required value
        $shortopts .= "v::"; // Optional value
        $shortopts .= "abc"; // These options do not accept values

        $longopts = array(
            "required:",     // Required value
            "optional::",    // Optional value
            "option",        // No value
            "opt",           // No value
        );
*/
        $this->options = getopt($shortOpts, $longOpts);
    }

    /**
     * @return array
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * @param string $sName
     * @param null $default
     * @return mixed|null
     */
    public function getOption($sName = '', $default = null) {
        return key_exists($sName, $this->options) ? $this->options[$sName] : $default;
    }

    /**
     * @return array
     */
    public function __invoke()
    {
        // TODO: Implement __invoke() method.
        return $this->options;
    }

}