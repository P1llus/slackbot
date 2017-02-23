<?php

namespace Pillus\Slackbot\Modules\Virustotal;

use Pillus\Slackbot\Modules\Virustotal\Virustotal;

/**
* This module handles all communication with Virustotal, including URL and IP searches, 
* and gathering reports from finalized scans.
**/

class Plugin
{

    /**
     * @var Botman
     */
    protected $botman;

    /**
     * @var Virustotal
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

    /**
    * Initiates all available commands used by the Shodan module.
    **/

    public function init()
    {
        $this->botman->hears('!vt ip {ip}', self::class.'@handleIpSearch');
        $this->botman->hears('!vt url {url}', self::class.'@handleUrlSearch');
    }

    /**
    * Checks Virustotal if the IP address is malicious.
    **/

    public function handleIpSearch($bot, $ip)
    {
        $results = $this->service->ipSearch($ip);
        
        # Sanity check for the returning response from the API.
        if (is_bool($results) || count(array_get($results, 'detected_urls')) === 0)
        {
            $reply[] = sprintf('*No malicious domains was found on %s*', $ip);
            $bot->reply(implode(PHP_EOL, $reply));
            return;
        }

        # Retrieves the results from the IP address
        else
        {
            $detected = array_get($results, 'detected_urls');

            $reply[] = sprintf('*Your Results for:* %s', $ip);
            $reply[] = sprintf('*Malicious domains found:* %s', count($detected));
            
                foreach ($detected as $url) 
                {
                    $url = array_get($url, 'url');
                    $reply[] = sprintf('*URL:* %s', $url);
                }
                
            $reply[] = sprintf('*For more information, go to:* https://www.virustotal.com/en/ip-address/%s/information', $ip);

            $bot->reply(implode(PHP_EOL, $reply));
            return;
        }
    }

    /**
    * Checks Virustotal if the URL address is malicious.
    **/

    public function handleUrlSearch($bot, $url)
    {
        $url = explode('|', substr($url, 1, -1))[0];
        
        $scan_id = $this->service->urlSearch($url);
        $results = $this->service->urlResult($url, $scan_id['scan_id']);

        # Sanity check for the returning response from the API.
        if (is_bool($results) || count(array_get($results, 'positives')) === 0)
        {
            $reply [] = sprintf('*No results found for:* %s.' . PHP_EOL . 
                ' A scan is most likely being run right now, please try with the same URL in a short while', $url);

            $bot->reply(implode(PHP_EOL, $reply));
            return;
        }
        
        # Retrieves the results of malicious domains from the specified URL
        else
        {
            $positives = array_get($results, 'positives');
            $total = array_get($results, 'total');

            $reply = [
                sprintf('*Waiting for results*'),
                sprintf('*Scan Finished*: %s thinks that this site might be malicious, out of %s vendors', $positives, $total)
            ];

            $bot->reply(implode(PHP_EOL, $reply));
            return;
        }
    }
}
