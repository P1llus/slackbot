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
        $this->botman->hears('!shodan query {query}', self::class.'@handleShQuerySearch');
        $this->botman->hears('!shodan heartbleed {target}', self::class.'@handleShHbSearch');
        $this->botman->hears('!shodan vuln {target}', self::class.'@handleShVulnSearch');
        $this->botman->hears('!shodan listqueries', self::class.'@handleShListQuerySearch');
    }

    public function handleShIpSearch($bot, $ip)
    {
        $results = $this->service->ipSearch($ip);
        $reply = [
            'Your Results for: '.$ip,
            'Target is running on: '.$results['data'][0]['os'],
            'Company: '.$results['data'][0]['asn'],
            'ISP is: '.$results['data'][0]['isp'],
            'Originates from: '.$results['country_name'],
            'Ports open are: ',
        ];

        if (isset($results['data']) && count($results['data']) > 0) {
            foreach ($results['data'] as $data) {
                $reply[] = $data['transport'].' : '.$data['port'].' - '.$data['product'];
            }
        }
    
        $reply[] = 'More results can be found on: '.'https://www.shodan.io/host/'.$ip;

        $bot->reply(implode(PHP_EOL, $reply));
    }

    public function handleShQuerySearch($bot, $query, $facets = null)
    {
        $results = $this->service->querySearch($query, $facets);
        $reply = [
            'Your Results for: '.$query,
        ];
        if (isset($results['matches']) &&count($results['matches']) > 0) {
            foreach ($results['matches'] as $matches) {
                $reply[] = 'IP: ' . $matches['ip_str']. ' - ' . 'Port: ' . $matches['port'] . ' - '. 'Org: ' . $matches['org'];
            }
        }

        $bot->reply(implode(PHP_EOL, $reply));
    }

    public function handleShHbSearch($bot, $target)
    {
        $results = $this->service->hbSearch($target);
        if (isset($results['data'][0]['opts']['heartbleed']) || count($results['data'] === 0))
        {
            $vuln = 'Vulnerable';
        }

        else 
        {
            $vuln = 'Not Vulnerable';
        }

        $reply = [
            'Target: ' . $target . ' is ' . $vuln,
        ];

        $bot->reply(implode(PHP_EOL, $reply));
    }

    public function handleShVulnSearch($bot, $target)
    {
        $results = $this->service->vulnSearch($target);
        $reply = [
            'Results for ' . $target . ':',
            $target . ' has ' . count($results['vulns']) . ' Vulnerabilitie(s)',
        ];
        
        foreach ($results['vulns'] as $vulns) {
            $reply[] = 'CVE: ' . $vulns;
        }

        $bot->reply(implode(PHP_EOL, $reply));
    }

    public function handleShListQuerySearch($bot)
    {
        $results = $this->service->listQuerySearch();
        var_dump($results);
        $reply = [
            'Popular queries are: '
        ];
        
        foreach ($results['matches'] as $query) {
            $reply[] = $query['query'];
        }

        $bot->reply(implode(PHP_EOL, $reply));
    }
}
