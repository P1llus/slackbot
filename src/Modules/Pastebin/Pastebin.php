<?php

namespace Pillus\Slackbot\Modules\Pastebin;

use GuzzleHttp\Client;

class Pastebin
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
    * Log in to Pastebin, this is so that all pastes are under a specific user, instead of guest
    */


    public function login()
    {
        $client = new Client([
            'base_uri' => 'http://pastebin.com/api/api_login.php',
            'form_params' => [
                'api_dev_key'       =>  $this->config['pastebin']['public_key'],
                'api_user_name'     =>  $this->config['pastebin']['user_name'],
                'api_user_password' =>  $this->config['pastebin']['user_password'],
            ],
        ]);
        $response = $client->request('POST');
        return $response->getBody()->getContents();
    }

    /**
    * Send a new paste to pastebin.com
    */

    public function paste($login, $data)
    {
        $client = new Client([
            'base_uri' => 'http://pastebin.com/api/api_post.php',
            'form_params' => [
                'api_dev_key'           =>  $this->config['pastebin']['public_key'],
                'api_user_key'          =>  $login,
                'api_paste_code'        =>  $data,
                'api_paste_private'     =>  '1',
                'api_paste_expiration'  =>  '1M',
                'api_paste_name'        =>  'testpaste',
                'api_option'            =>  'paste',

            ],
        ]);
        $response = $client->request('POST');
        return $response->getBody()->getContents();
    }
};
