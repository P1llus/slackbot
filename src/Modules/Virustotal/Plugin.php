<?php

namespace Pillus\Slackbot\Modules\Virustotal;

use Pillus\Slackbot\Modules\Virustotal\Virustotal;

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
        $reply = [
            'Your Results for: '.$ip,
        ];

        if (isset($results['detected_urls']) && count($results['detected_urls']) > 0) {
            $reply[] = 'Malicious domains found: '. count($results['detected_urls']);
            foreach ($results['detected_urls'] as $url) {
                $reply[] = 'URL: '.$url['url'];
            }
        } else {
            $reply[] = 'None malicious domains was found';
        }

        $reply[] = 'For more information go to: '.'https://www.virustotal.com/en/ip-address/'.$ip.'/information';
        $bot->reply(implode(PHP_EOL, $reply));
    }

    public function handleUrlSearch($bot, $url)
    {
        # Slack messes up the url, so you have to reformat it
        $url = explode('|', substr($url, 1, -1))[0];
        
        $scan_id = $this->service->urlSearch($url);
        $results = $this->service->urlResult($url, $scan_id['scan_id']);
        $reply = [
            'Waiting for results',
            'Scan Finished, '.$results['positives'].' thinks that this site might be malicious, out of '.$results['total'].' vendors.',
            'For a full report, go to: '.$results['permalink'],
        ];

        $bot->reply(implode(PHP_EOL, $reply));
    }
}
