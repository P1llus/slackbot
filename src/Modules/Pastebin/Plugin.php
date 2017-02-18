<?php

namespace Pillus\Slackbot\Modules\Pastebin;

use Pillus\Slackbot\Modules\Pastebin\Pastebin;

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
        $this->service = new Pastebin;
    }

    public function setup($botman)
    {
        $this->botman = $botman;

        return $this;
    }

    public function init() 
    {
        $this->botman->hears('!pbpaste {data}', self::class.'@handlepbPaste');
    }

    public function handlePbPaste($bot, $data) 
    {
        $login = $this->service->login();
        $paste = $this->service->paste($login, $data);
        $bot->reply('Your paste is: '.$paste);
    }
}
