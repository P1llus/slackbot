<?php

namespace Pillus\Slackbot\Modules\Help;

use GuzzleHttp\Client;

class Help
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
    * Check a Wordpress version for information on wpvulndb.com
    */

    public function listCommands()
    {
        $commands =
        [
            '*!vt ip [IP Address, example 192.168.1.1]* - Checks virustotal if the IP address is safe',
            '*!vt url [URL, example www.microsoft.com]* - Checks virustotal if the URL is safe',
            '*!wpscan version [VERSION, example 4.3]* - Lists all vulnerabilities to a specific wordpress version]',
            '*!wpscan plugin [PLUGIN, example eshop]* - Lists all vulnerabilities related to a specific wordpress plguin]',
            '*!shodan ip [IP Address, example 10.0.0.0]* - Lists information from Shodan about the IP',
            '*!shodan query [QUERY, example hostname:something]* - Uses all the same queries as Shodan does, gives you a list of IP addresses in return, that matches this query',
            '*!shodan heartbleed [IP, example 10.0.0.0]* - Returns if target is vulnerable to Heartbleed',
            '*!shodan vuln [IP, example 100.10.0.0]* - Returns all known vulnerabilties for IP address',
            '*!shodan listqueries* - Returns popular queries that is used on Shodan',
            '*!haveibeenpwned account [Account name, example Terminator]* - This checks ihasbeenpwned if your Username/Account name has ever been mentioned in any password leaks',
            '*!haveibeenpwned email [EMAIL, example test@example.com]* - This checks ihasbeenpwned if your Email account is mentioned in any password leaks',
            '*!help* - Returns this list',
        ];

        return $commands;
    }
};
