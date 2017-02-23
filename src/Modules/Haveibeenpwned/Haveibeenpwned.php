<?php

namespace Pillus\Slackbot\Modules\Haveibeenpwned;

use GuzzleHttp\Client;
use Pillus\Slackbot\Helpers\Grabinfo;

class Haveibeenpwned
{
    public $config;

    /**
    * Grab the config file for this Module
    */

    public function __construct()
    {
        $this->config = require('config.php');
        $this->baseurl = 'https://haveibeenpwned.com/api/v2/';
        $this->grab = new Grabinfo;
    }
    
    /**
    * Check a account for possible breaches
    */

    public function accountSearch($account)
    {
        $data = [
            'base_uri' => sprintf($this->baseurl . '/breachedaccount/%s', urlencode($account)),
        ];

        return $this->grab->grab($data, 'GET');
    }

    /**
    * List all public credentials listed for an email
    */

    public function emailPasteSearch($email)
    {
        $email = explode('|', substr($email, 1, -1))[1];
        $data = [
            'base_uri' => sprintf($this->baseurl . 'pasteaccount/%s', urlencode($email)),
        ];

        return $this->grab->grab($data, 'GET');
    }
};
