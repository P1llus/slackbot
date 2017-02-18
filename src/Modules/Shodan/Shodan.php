<?php

namespace Pillus\Slackbot\Modules\Shodan;

use GuzzleHttp\Client;

class Shodan
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
    * Check a IP for information on Shodan.io
    */

    public function ipSearch($ip)
    {
        $client = new Client([
            'base_uri' => 'https://api.shodan.io/shodan/host/'.$ip,
            'query' => [
                'key'   =>  $this->config['shodan']['public_key'],
            ],
        ]);
        $response = $client->request('GET');
        return json_decode($response->getBody()->getContents(), true);
    }

};

