<?php

namespace Pillus\Slackbot\Modules\Virustotal;

use GuzzleHttp\Client;
use Pillus\Slackbot\Helpers\Grabinfo;

class Virustotal
{
    public $config;

    /**
    * Grab the settings for this module
    */

    public function __construct()
    {
        $this->config = require('config.php');
        $this->grab = new Grabinfo;
        $this->key = array_get($this->config, 'virustotal.public_key');
        $this->baseurl = 'http://www.virustotal.com/vtapi/v2/';
    }
    
    /**
    * Searching Virustotal for information about a IP Address
    */

    public function ipSearch($ip)
    {
        $data = [
            'base_uri' => sprintf($this->baseurl . 'ip-address/report'),
            'query' => [
                'apikey'        =>  $this->key,
                'ip'            =>  $ip,
            ],
        ];
        
        return $this->grab->grab($data, 'GET');
    }

    /**
    * Searching Virustotal for information about a URL
    */

    public function urlSearch($url)
    {
        $data = [
            'base_uri' => sprintf($this->baseurl . 'url/scan'),
            'query' => [
                'apikey'        =>  $this->key,
                'url'           =>  $url,
            ],
        ];
        
        return $this->grab->grab($data, 'GET');
    }

    /**
    * Searching Virustotal for updates on a ongoing search
    */

    public function urlResult($url, $scan_id)
    {
        $data = [
            'base_uri' => sprintf($this->baseurl . 'url/report'),
            'query' => [
                'apikey'        =>  $this->key,
                'resource'      =>  $url,
                'scan_id'       =>  $scan_id
            ],
        ];

        return $this->grab->grab($data, 'GET');
    }
};
