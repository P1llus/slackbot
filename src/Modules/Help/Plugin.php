<?php

namespace Pillus\Slackbot\Modules\Help;

use Pillus\Slackbot\Modules\Help\Help;

/**
* This modules handles all help commands used by the bot
**/

class Plugin
{

    /**
     * @var Botman
     */
    protected $botman;

    /**
     * @var Help
     */
    protected $service;

    public function __construct()
    {
        $this->service = new Help;
    }

    public function setup($botman)
    {
        $this->botman = $botman;

        return $this;
    }

    public function init()
    {
        $this->botman->hears('!help', self::class.'@handleHelp');
    }

    public function handleHelp($bot)
    {
        $results = $this->service->listCommands();
        $reply = [
            'The current supported commands are:',
        ];

        foreach ($results as $result) {
            $reply[] = $result . PHP_EOL;
        }

        $bot->reply(implode(PHP_EOL, $reply));
    }
}
