<?php

namespace Pillus\Slackbot\Modules\Wpscan;

use GuzzleHttp\Client;
use Pillus\Slackbot\Helpers\Grabinfo;

class Wpscan
{
    public $config;

    /**
    * Grab the config file for this Module
    */

    public function __construct()
    {
        $this->config = require('config.php');
        $this->grab = new Grabinfo;
        $this->baseurl = 'https://wpvulndb.com/api/v2/';
    }
    
    /**
    * Check a Wordpress version for information on wpvulndb.com
    */

    public function versionSearch($version)
    {
        $data = [
            'base_uri' => sprintf($this->baseurl . 'wordpresses/%s', $version),
        ];

        return $this->grab->grab($data, 'GET');
    }

    /**
    * Check a Wordpress plugin for information on wpvulndb.com
    */

    public function pluginSearch($plugin)
    {
        $data = [
            'base_uri' => sprintf($this->baseurl . 'plugins/%s', $plugin),
        ];

        return $this->grab->grab($data, 'GET');
    }
};
