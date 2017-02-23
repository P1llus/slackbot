<?php

namespace Pillus\Slackbot\Modules\Shodan;

use GuzzleHttp\Client;
use Pillus\Slackbot\Helpers\Grabinfo;

class Shodan
{
    public $config;

    /**
    * Grabs the configuration and a few class wide variables for this Module
    **/

    public function __construct()
    {
        $this->config = require('config.php');
        $this->grab = new Grabinfo;
        $this->key = array_get($this->config, 'shodan.public_key');
        $this->baseurl = 'https://api.shodan.io/shodan/';
    }
    
    /**
    * Checks an IP address for information on Shodan.io
    **/

    public function ipSearch($ip)
    {
        $data = [
            'base_uri' => sprintf($this->baseurl . 'host/%s', $ip),
            'query' => [
                'key'   =>  $this->key,
            ],
        ];

        return $this->grab->grab($data, 'GET');
    }

    /**
    * Runs a Shodan specific query against their API
    **/

    public function querySearch($query, $facets = null)
    {
        $data = [
            'base_uri' => sprintf($this->baseurl . 'host/search'),
            'query' => [
                'key'   =>  $this->key,
                'query'   =>  $query,
                'facets'   =>  $facets,
            ],
        ];
        return $this->grab->grab($data, 'GET');
    }

    /**
    * Checks an IP address if it is vulnerable against Heartbleed
    **/

    public function hbSearch($target)
    {
        $data = [
            'base_uri' => sprintf($this->baseurl . 'host/%s', $target),
            'query' => [
                'key'   =>  $this->key,
            ],
        ];
        
        return $this->grab->grab($data, 'GET');
    }

    /**
    * Checks an IP address for known vulnerabilities
    **/

    public function vulnSearch($target)
    {
        $data = [
            'base_uri' => sprintf($this->baseurl . 'host/%s', $target),
            'query' => [
                'key'   =>  $this->key,
            ],
        ];
        
        return $this->grab->grab($data, 'GET');
    }

    /**
    * Lists popular public queries that others have used
    **/

    public function listQuerySearch()
    {
        $data = [
            'base_uri' => sprintf($this->baseurl . 'query'),
            'query' => [
                'key'   =>  $this->key,
            ],
        ];
        
        return $this->grab->grab($data, 'GET');
    }
};
