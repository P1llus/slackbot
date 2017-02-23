<?php

namespace Pillus\Slackbot\Modules\Pastebin;

use GuzzleHttp\Client;
use Pillus\Slackbot\Helpers\Grabinfo;

class Pastebin
{
    public $config;

    /**
    * Grab the config file for this Module
    */

    public function __construct()
    {
        $this->config = require('../Config/config.php');
        $this->key = array_get($this->config, 'shodan.public_key');
        $this->username = array_get($this->config, 'pastebin.user_name')
        $this->password = array_get($this->config, 'pastebin.user_password')
        $this->baseurl = 'http://pastebin.com/api/';
    }
    
    /**
    * Log in to Pastebin, this is so that all pastes are under a specific user, instead of guest
    */


    public function login()
    {
        $data = [
            'base_uri' => sprintf($this->baseurl . 'api_login.php'),
            'form_params' => [
                'api_dev_key'       =>  $this->key,
                'api_user_name'     =>  $this->username,
                'api_user_password' =>  $this->password,
            ],
        ];

        return $this->grab->grab($data, 'GET');
    }

    /**
    * Send a new paste to pastebin.com
    */

    public function paste($data)
    {
        $login = login();
        $data = [
            'base_uri' => 'http://pastebin.com/api/api_post.php',
            'form_params' => [
                'api_dev_key'           =>  $this->key,
                'api_user_key'          =>  $login,
                'api_paste_code'        =>  $data,
                'api_paste_private'     =>  '1',
                'api_paste_expiration'  =>  '1M',
                'api_paste_name'        =>  'testpaste',
                'api_option'            =>  'paste',

            ],
        ];
        return $this->grab->grab($data, 'POST');
    }
};
