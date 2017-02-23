<?php

namespace Pillus\Slackbot\Modules\Wpscan;

use Pillus\Slackbot\Modules\Wpscan\Wpscan;

/**
* This module intergrates with the API of WPSCAN DB, to look for vulnerabilities related
* to wordpress themes, versions and plugins.
**/

class Plugin
{

    /**
     * @var Botman
     */
    protected $botman;

    /**
     * @var Wpscan
     */
    protected $service;

    public function __construct()
    {
        $this->service = new Wpscan;
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
        $this->botman->hears('!wpscan version {version}', self::class.'@handleVersionSearch');
        $this->botman->hears('!wpscan plugin {plugin}', self::class.'@handlePluginSearch');
    }

    /**
    * Lists vulnerabilities related to specific wordpress versions.
    **/

    public function handleVersionSearch($bot, $version)
    {
        $results = $this->service->versionSearch(str_replace('.', '', $version));
        
        # Sanity check for the returning response from the API.
        if (is_bool($results) || count(array_get($results, $version)) === 0)
        {
            $reply[] = sprintf('*No results found for version:* %s', $version);
            $bot->reply(implode(PHP_EOL, $reply));
            return;
        }

        # Returns all vulnerabilities related to a specific wordpress version
        else
        {
            $reply = [
                sprintf('Your Results for Wordpress version: %s', $version),
            ];

            $version = array_get($results, $version, []);

            $vulns = array_get($version, 'vulnerabilities');
            $reply[] = sprintf('Vulnerabilities found: %s', count($vulns)) . PHP_EOL;

            foreach ($vulns as $vuln) {
                $title = array_get($vuln, 'title');
                $date = date('d/m/Y', strtotime(array_get($vuln, 'published_date')));
                $urls = array_get($vuln, 'references.url');
                $fixed = array_get($vuln, 'fixed_in');

                $reply[] = sprintf('*Title*: ', $title);
                $reply[] = sprintf('*Published at*: %s', $date);

                foreach ($urls as $url) {
                    $reply[] = sprintf('*Url*: %s', $url);
                }

                $reply[] = sprintf('*Fixed In*: %s', $fixed) . PHP_EOL;
            }
            $bot->reply(implode(PHP_EOL, $reply));
            return;
        }
    }

    /**
    * Lists vulnerabilities related to specific wordpress plugin.
    **/

    public function handlePluginSearch($bot, $plugin)
    {
        $results = $this->service->pluginSearch($plugin);

        # Sanity check for the returning response from the API.
        if (is_bool($results) || count(array_get($results, $plugin)) === 0)
        {
            $reply[] = sprintf('*No Vulnerabilities found for this plugin*');

            $bot->reply(implode(PHP_EOL, $reply));
            return;
        }

        # Returns all vulnerabilities related to a specific wordpress plugin
        else
        {

            $vulns = array_get($results, $plugin . '.vulnerabilities');

            $reply = [
            sprintf('*Your Results for Wordpress Plugin:* %s', $plugin),
            sprintf('*Vulnerabilities found: %s*', count($vulns)) . PHP_EOL
            ];

            foreach ($vulns as $vuln) 
            {
                $title = array_get($vuln, 'title');
                $date = date('d/m/Y', strtotime(array_get($vuln, 'published_date')));
                $urls = array_get($vuln, 'references.url', []);
                $fixed = array_get($vuln, 'fixed_in');

                $reply[] = sprintf('*Title*: %s', $title);
                $reply[] = sprintf('*Published at*: %s', $date);

                foreach ($urls as $url) 
                {
                    $reply[] = sprintf('*Url*: %s', $url);
                }

                if (count($fixed) === 0)
                {
                    $fixed = 'Unknown';
                }

            $reply[] = sprintf('*Fixed In*: %s', $fixed) . PHP_EOL;
            
            }

            $bot->reply(implode(PHP_EOL, $reply));
            return;
        }
    }
}
