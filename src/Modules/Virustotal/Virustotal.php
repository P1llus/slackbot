<?php

namespace Pillus\Slackbot\Modules\Virustotal;

use GuzzleHttp\Client;

class Virustotal
{
    public $config;

    /**
    * Grab the config file for this Module
    */

    public function __construct()
    {
        $this->config = require('config.php');
    }
    
    /**
    * Searching Virustotal for information about a IP Address
    */

    public function ipSearch($ip)
    {
        $client = new Client([
            'base_uri' => 'http://www.virustotal.com/vtapi/v2/ip-address/report',
            'query' => [
                'apikey'        =>  $this->config['virustotal']['public_key'],
                'ip'            =>  $ip,
            ],
        ]);
        $response = $client->request('GET');
        return json_decode($response->getBody()->getContents(), true);
    }

    public function urlSearch($url)
    {
        $client = new Client([
            'base_uri' => 'http://www.virustotal.com/vtapi/v2/url/scan',
            'query' => [
                'apikey'        =>  $this->config['virustotal']['public_key'],
                'url'           =>  $url,
            ],
        ]);
        $response = $client->request('POST');
        return json_decode($response->getBody()->getContents(), true);
    }

    public function urlResult($url, $scan_id)
    {
        $client = new Client([
            'base_uri' => 'http://www.virustotal.com/vtapi/v2/url/report',
            'query' => [
                'apikey'        =>  $this->config['virustotal']['public_key'],
                'resource'      =>  $url,
                'scan_id'       =>  $scan_id
            ],
        ]);
        $response = $client->request('POST');
        return json_decode($response->getBody()->getContents(), true);
    }
};
