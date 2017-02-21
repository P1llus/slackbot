<?php

namespace Pillus\Slackbot\Modules\Wpscan;

use GuzzleHttp\Client;

class Wpscan
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
    * Check a Wordpress version for information on wpvulndb.com
    */

    public function versionSearch($version)
    {
        $client = new Client([
            'base_uri' => 'https://wpvulndb.com/api/v2/wordpresses/'.$version,
        ]);
        $response = $client->request('GET');
        return json_decode($response->getBody()->getContents(), true);
    }

    public function pluginSearch($plugin)
    {
        $client = new Client([
            'base_uri' => 'https://wpvulndb.com/api/v2/plugins/'.$plugin,
        ]);
        $response = $client->request('GET');
        return json_decode($response->getBody()->getContents(), true);
    }
};
