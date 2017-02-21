<?php

namespace Pillus\Slackbot\Modules\Haveibeenpwned;

use GuzzleHttp\Client;

class Haveibeenpwned
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
    * Check a account for possible breaches
    */

    public function accountSearch($account)
    {
        $client = new Client([
            'base_uri' => 'https://haveibeenpwned.com/api/v2/breachedaccount/'.urlencode($account),
        ]);

        $response = $client->request('GET');
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
    * List all public credentials listed for an email
    */

    public function emailPasteSearch($email)
    {
        $email = explode('|', substr($email, 1, -1))[1];
        $client = new Client([
            'base_uri' => 'https://haveibeenpwned.com/api/v2/pasteaccount/'. urlencode($email),
        ]);

        $response = $client->request('GET');
        return json_decode($response->getBody()->getContents(), true);
    }
};
