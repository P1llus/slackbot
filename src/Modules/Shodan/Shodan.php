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

    public function querySearch($query, $facets = null)
    {
        $client = new Client([
            'base_uri' => 'https://api.shodan.io/shodan/host/search',
            'query' => [
                'key'   =>  $this->config['shodan']['public_key'],
                'query'   =>  $query,
                'facets'   =>  $facets,
            ],
        ]);
        $response = $client->request('GET');
        return json_decode($response->getBody()->getContents(), true);
    }

    public function hbSearch($target)
    {
        $client = new Client([
            'base_uri' => 'https://api.shodan.io/shodan/host/'.$target,
            'query' => [
                'key'   =>  $this->config['shodan']['public_key'],
            ],
        ]);
        $response = $client->request('GET');
        return json_decode($response->getBody()->getContents(), true);
    }

    public function vulnSearch($target)
    {
        $client = new Client([
            'base_uri' => 'https://api.shodan.io/shodan/host/'.$target,
            'query' => [
                'key'   =>  $this->config['shodan']['public_key'],
            ],
        ]);
        $response = $client->request('GET');
        return json_decode($response->getBody()->getContents(), true);
    }

    public function listQuerySearch()
    {
        $client = new Client([
            'base_uri' => 'https://api.shodan.io/shodan/query',
            'query' => [
                'key'   =>  $this->config['shodan']['public_key'],
            ],
        ]);
        $response = $client->request('GET');
        return json_decode($response->getBody()->getContents(), true);
    }
};
