<?php

/**
* Get the Autoloader
* This will also load namespace for our src folder
*/

require 'vendor/autoload.php';

/**
* Get all the basic stuff
*/

use React\EventLoop\Factory;
use Mpociot\BotMan\BotManFactory;

/**
* Import the plugins
*/

use Pillus\Slackbot\Modules\Shodan\Plugin as ShodanPlugin;
use Pillus\Slackbot\Modules\Wpscan\Plugin as WpscanPlugin;
use Pillus\Slackbot\Modules\Pastebin\Plugin as PastebinPlugin;
use Pillus\Slackbot\Modules\Virustotal\Plugin as VirustotalPlugin;


/**
* Load config file for the bot itself
*/

$config = require __DIR__.'/../src/Config/config.php';

/**
* Start up a botman instance
*/

$loop = Factory::create();
$botman = BotManFactory::createForRTM([
    'slack_token' => $config['slack']['public_key'],
], $loop);

/**
* Load all plugins into the running Botman instance
*/

$plugins = [
    (new PastebinPlugin)->setup($botman),
    (new VirusTotalPlugin)->setup($botman),
    (new ShodanPlugin)->setup($botman),
    (new WpscanPlugin)->setup($botman),
];

/**
* Initiate all plugins
*/

foreach ($plugins as $plugin) {
    $plugin->init();
}

/**
* Help Commands
*/



/**
* Initiate the bot instance
*/

$loop->run();
