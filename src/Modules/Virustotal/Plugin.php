<?php

namespace Pillus\Slackbot\Modules\Virustotal;

use Pillus\Slackbot\Modules\Virustotal\Virustotal;

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
        $this->service = new Virustotal;
    }

    public function setup($botman)
    {
        $this->botman = $botman;

        return $this;
    }

    public function init() 
    {
        $this->botman->hears('!vt ip {ip}', self::class.'@handleIpSearch');
        $this->botman->hears('!vt url {url}', self::class.'@handleUrlSearch');
    }

    public function handleIpSearch($bot, $ip)
    {
        $results = $this->service->ipSearch($ip);
        $bot->reply('Your results for '.$ip.' is:');
        $bot->reply('Malicious domains found: '. count($results['detected_urls']));
        
        # Dirty hack to make sure that domain count is shown on screen before the url's
        sleep(5);

        if(count($results['detected_urls']) > 0) 
        {
            foreach ($results['detected_urls'] as $url)
            {
                $bot->reply('URL: '.$url['url']);
            }
        }
        $bot->reply('For more information go to: '.'https://www.virustotal.com/en/ip-address/'.$ip.'/information');
    }

    public function handleUrlSearch($bot, $url)
    {
        # Slack messes up the url, so you have to reformat it
        $url = explode('|', substr($url, 1, -1))[0];

        $bot->reply('Waiting for results');
        $scan_id = $this->service->urlSearch($url);
        $results = $this->service->urlResult($url, $scan_id['scan_id']);
        sleep(10);
        $bot->reply('Scan Finished, '.$results['positives'].' thinks that this site might be malicious, out of '.$results['total'].' vendors.');
        $bot->reply('For a full report, go to: '.$results['permalink']);
    }
}
