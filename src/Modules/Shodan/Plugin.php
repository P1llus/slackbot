<?php

namespace Pillus\Slackbot\Modules\Shodan;

use Pillus\Slackbot\Modules\Shodan\Shodan;

class Plugin
{

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
        $this->botman->hears('!shodan url {url}', self::class.'@handleShIpSearch');
    }

    public function handleShIpSearch($bot, $ip)
    {
        $results = $this->service->ipSearch($ip);
        $reply = [
            'YOur Results for: '.$ip,
            'Target is running on: '.$results['data'][0]['os'],
            'Company: '.$results['data'][0]['asn'],
            'ISP is: '.$results['data'][0]['isp'],
            'Originates from: '.$results['country_name'],
            'Ports open are: ',
        ];

        if (count($results['data']) > 0) {
            foreach ($results['data'] as $data) {
                $reply[] = $data['transport'].' : '.$data['port'].' - '.$data['product'];
            }
        }
    
        $reply[] = 'More results can be found on: '.'https://www.shodan.io/host/'.$ip;

        $bot->reply(implode(PHP_EOL, $reply));
    }
}
