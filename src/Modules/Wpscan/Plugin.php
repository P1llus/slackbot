<?php

namespace Pillus\Slackbot\Modules\Wpscan;

use Pillus\Slackbot\Modules\Wpscan\Wpscan;

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
        $this->service = new Wpscan;
    }

    public function setup($botman)
    {
        $this->botman = $botman;

        return $this;
    }

    public function init()
    {
        $this->botman->hears('!wpscan version {version}', self::class.'@handleVersionSearch');
        $this->botman->hears('!wpscan plugin {plugin}', self::class.'@handlePluginSearch');
    }

    public function handleVersionSearch($bot, $version)
    {
        $results = $this->service->versionSearch(str_replace('.', '', $version));
        $reply = [
            'Your Results for Wordpress version: '.$version,
        ];

        // if nothing found, just drop out here
        if (!isset($results[$version]) || count($results[$version]) === 0) {
            $reply[] = 'No Vulnerabilities found';

            $bot->reply(implode(PHP_EOL, $reply));
            return;
        }

        $version = array_get($results, $version, []);

        $vulns = array_get($version, 'vulnerabilities');
        $reply[] = 'Vulnerabilities found: '. count($vulns);

        foreach ($vulns as $vuln) {
            $reply[] = '*Title*: '. array_get($vuln, 'title');
            $reply[] = '*Published at*: '. date('d/m/Y', strtotime(array_get($vuln, 'published_date')));

            foreach (array_get($vuln, 'references.url', []) as $url) {
                $reply[] = '*Url*: '.$url;
            }

            $reply[] = '*Fixed In*: '. array_get($vuln, 'fixed_in');
        }

        $bot->reply(implode(PHP_EOL, $reply));
    }

    public function handlePluginSearch($bot, $plugin)
    {
        $results = $this->service->pluginSearch(str_replace('.', '', $plugin));
        $reply = [
            'Your Results for Wordpress Plugin: '.$plugin,
        ];

        // if nothing found, just drop out here
        if (!isset($results[$plugin]) || count($results[$plugin]) === 0) {
            $reply[] = 'No Vulnerabilities found for this plugin';

            $bot->reply(implode(PHP_EOL, $reply));
            return;
        }

        $plugin = array_get($results, $plugin, []);

        $vulns = array_get($plugin, 'vulnerabilities');
        $reply[] = 'Vulnerabilities found: '. count($vulns);

        foreach ($vulns as $vuln) {
            $reply[] = '*Title*: '. array_get($vuln, 'title');
            $reply[] = '*Published at*: '. date('d/m/Y', strtotime(array_get($vuln, 'published_date')));

            foreach (array_get($vuln, 'references.url', []) as $url) {
                $reply[] = '*Url*: '.$url;
            }

            $reply[] = '*Fixed In*: '. array_get($vuln, 'fixed_in');
        }

        $bot->reply(implode(PHP_EOL, $reply));
    }
}
