<?php

namespace Pillus\Slackbot\Modules\Shodan;

use Pillus\Slackbot\Modules\Shodan\Shodan;

class Plugin {

    /**
     * @var Botman
     */
    protected $botman;

    /**
     * @var Pastebin
     */
    protected $service;

    public function __construct()
    {
        $this->service = new Shodan;
    }

    public function setup($botman)
    {
        $this->botman = $botman;

        return $this;
    }

    public function init() 
    {
        $this->botman->hears('!shodan ip {ip}', self::class.'@handleShIpSearch');
    }

    public function handleShIpSearch($bot, $ip) 
    {
        $results = $this->service->ipSearch($ip);
        $bot->reply('Results for: '.$ip);
        $bot->reply('Target is running on: '.$results['data'][0]['os']);
        $bot->reply('Company: '.$results['data'][0]['asn']);
        $bot->reply('ISP is: '.$results['data'][0]['isp']);
        $bot->reply('Originates from: '.$results['country_name']);
        $bot->reply('Ports open are:');
        if(count($results['data']) > 0) 
        {
            foreach ($results['data'] as $data)
            {
                $bot->reply($data['transport'].' : '.$data['port'].' - '.$data['product']);
            }
        }
        $bot->reply('More results can be found on: '.'https://www.shodan.io/host/'.$ip);
    }
}
