<?php

namespace Pillus\Slackbot\Modules\Shodan;

use Pillus\Slackbot\Modules\Shodan\Shodan;

/**
* This module intergrates with the API by the vulnerability search engine Shodan.io
* These API calls will require a API key that can be filled out in the config file 
* located in the Module root folder.
**/

class Plugin
{

    /**
     * @var Botman
     **/
    protected $botman;

    /**
     * @var Shodan
     **/
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

    /**
    * Initiates all available commands used by the Shodan module.
    **/

    public function init()
    {
        $this->botman->hears('!shodan ip {ip}', self::class.'@handleShIpSearch');
        $this->botman->hears('!shodan query {query}', self::class.'@handleShQuerySearch');
        $this->botman->hears('!shodan heartbleed {target}', self::class.'@handleShHbSearch');
        $this->botman->hears('!shodan vuln {target}', self::class.'@handleShVulnSearch');
        $this->botman->hears('!shodan listqueries', self::class.'@handleShListQuerySearch');
    }

    /**
    * Handles searching for IP Addresses, then formats the output before sending it to 
    * the destination.
    **/    

    public function handleShIpSearch($bot, $ip)
    {
        $results = $this->service->ipSearch($ip);

        # Sanity check for the returning response from the API.
        if (is_bool($results) || count(array_get($results, 'data')) === 0)
        {
            $reply[] = sprintf('*No results found for:* %s', $ip);
            $bot->reply(implode(PHP_EOL, $reply));
            return;
        }

        # Get all the returning information from the IP search and builds a reply.
        else 
        {
            $target = array_get($results, 'data.0.os');
            $company = array_get($results, 'data.0.asn');
            $isp = array_get($results, 'data.0.isp');
            $country = array_get($results, 'country_name');
            $results = array_get($results, 'data', []);

            if (count($target) === 0)
                    {
                        $target = 'Unknown';
                    }
                    
            $reply = [
                sprintf('*Your Results for*: %s', $ip), 
                sprintf('*Target is running on*: %s', $target),
                sprintf('*Company*: %s', $company),
                sprintf('*ISP*: %s', $isp),
                sprintf('*Originates from*: %s', $country),
                sprintf('*Ports open are*:'),
            ];
            
            # Gets all Ports and their products.
            foreach ($results as $result)
            {
                $transport = strtoupper(array_get($result, 'transport'));
                $port = array_get($result, 'port');
                $product = array_get($result, 'product');

                if (count($product) === 0)
                {
                    $product = 'Not known';
                }

                $reply[] = sprintf('*%s*: %s - %s', $transport, $port, $product);
            }

        $reply[] = sprintf('*More results can be found on*: https://www.shodan.io/host/%s', $ip);
        }

        $bot->reply(implode(PHP_EOL, $reply));
    }

    /**
    * Handles running queries, then formats the output before sending it to the 
    * destination.
    **/

    public function handleShQuerySearch($bot, $query, $facets = null)
    {
        $results = $this->service->querySearch($query, $facets);

        # Sanity check for the returning response from the API.
        if (is_bool($results) || count(array_get($results, 'matches')) === 0)
        {
            $reply [] = sprintf('*No results found for:* %s', $query);
            $bot->reply(implode(PHP_EOL, $reply));
            return;
        }
        
        # Gets the returning IP addresses, ports and organization names.
        else
        {
            $reply = [
                sprintf('*Your Results for:* %s', $query) . PHP_EOL,
            ];

            foreach (array_get($results, 'matches', []) as $match) {
                $reply[] = sprintf('*IP* %s - *Port:* %s - *Org:* %s',
                    array_get($match, 'ip_str'),
                    array_get($match, 'port'),
                    array_get($match, 'org'));
            }
        }

        $bot->reply(implode(PHP_EOL, $reply));
    }

    /**
    * Handles the Heartbleed searches, then formats the output before sending it to the 
    * destination.
    **/

    public function handleShHbSearch($bot, $target)
    {
        $results = $this->service->hbSearch($target);

        # Sanity check for the returning response from the API.
        if (is_bool($results) || count(array_get($results, 'data')) === 0)
        {
            $reply [] = sprintf('*No results found for:* %s', $target);
            $bot->reply(implode(PHP_EOL, $reply));
            return;
        }

        # Checks if the returning results has any information, if it does, it is vulnerable.
        elseif (array_get($results, 'data.0.opts.heartbleed') > 0)
        {
            $vuln = 'Vulnerable';
            $reply[] = sprintf('*Target:* %s is *%s*', $target, $vuln);
            $bot->reply(implode(PHP_EOL, $reply));
            return;
        }

        # If it has data, but nothing related to heartbleed.
        else
        {
            $vuln = 'Not Vulnerable';
            $reply[] = sprintf('*Target:* %s is *%s*', $target, $vuln);
            $bot->reply(implode(PHP_EOL, $reply));
            return;
        }
    }

    /**
    * Handles the vulnerability searches, then formats the output before sending it to
    * the destination.
    **/

    public function handleShVulnSearch($bot, $target)
    {
        $results = $this->service->vulnSearch($target);

        # Sanity check for the returning response from the API.
        if (is_bool($results) || count(array_get($results, 'vulns')) === 0)
        {
            $reply[] = sprintf('*No results found for:* %s', $target);
            $bot->reply(implode(PHP_EOL, $reply));
            return;
        }
        
        # Get's the returning vulnerabilities for a specific IP Address.
        else
        {
            $vulns = array_get($results, 'vulns');
            $reply = [
                sprintf('*Results for* %s:', $target),
                sprintf('*%s Vulnerabilitie(s) was found*', count($vulns)),
            ];
            
            foreach ($vulns as $vuln) 
            {
                $reply[] = sprintf('*CVE:* %s', $vuln);
            }

            $bot->reply(implode(PHP_EOL, $reply));
            return;
        }
    }

    /**
    * Handles the listing of commonly used queries, then formats the output before sending it to the 
    * destination.
    **/

    public function handleShListQuerySearch($bot)
    {
        $results = $this->service->listQuerySearch();
        
        # Sanity check for the returning response from the API.
        if (is_bool($results) || count(array_get($results, 'matches')) === 0)
        {
            $reply [] = sprintf('*A problem occured*');
            $bot->reply(implode(PHP_EOL, $reply));
            return;
        }

        # Returns commonly used queries.
        else
        {
            $reply = [
                sprintf('*Popular queries are:* ')
            ];
            
            foreach (array_get($results, 'matches', []) as $query) 
            {
                $reply[] = sprintf('*Query:* %s', array_get($query, 'query'));
            }

            $bot->reply(implode(PHP_EOL, $reply));
            return;
        }
    }
}
