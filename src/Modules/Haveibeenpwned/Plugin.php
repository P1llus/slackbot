<?php

namespace Pillus\Slackbot\Modules\Haveibeenpwned;

use Pillus\Slackbot\Modules\Haveibeenpwned\Haveibeenpwned;

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
        $this->service = new Haveibeenpwned;
    }

    public function setup($botman)
    {
        $this->botman = $botman;

        return $this;
    }

    public function init()
    {
        $this->botman->hears('!haveibeenpwned account {account}', self::class.'@handleAccountSearch');
        $this->botman->hears('!haveibeenpwned emailpaste {email}', self::class.'@handleEmailPasteSearch');
    }

    public function handleAccountSearch($bot, $account)
    {
        $results = $this->service->AccountSearch($account);
        foreach ($results as $result)
        {
            $reply[] = 'Title: ' . $result['Title'];
            $reply[] = 'Breach Date: ' . $result['BreachDate'];
            $reply[] = 'Verified leak?: ' . ($result['IsVerified'] ? 'Yes' : 'No') . PHP_EOL;
        }
        
        $bot->reply(implode(PHP_EOL, $reply));
    }

    public function handleEmailPasteSearch($bot, $email)
    {
        $results = $this->service->emailPasteSearch($email);
        if (isset($results) || count($results) > 0) 
        {
            $reply[] = 'Your email has been leaked ' . count($results) . ' Times';
            foreach ($results as $result)
            {
                
                if (isset($result['Title']))
                {
                   $reply[] = '*Title:* ' . $result['Title']; 
                }
                else
                {
                    $reply[] = '*Title:* No title found';
                }
                    
                $reply[] = '*Source:* ' . $result['Source']; 

                if ($result['Source'] == 'Pastebin')
                {
                    $reply[] = '*URL:* ' . 'http://www.pastebin.com/' . $result['Id'] . PHP_EOL;
                }
                
                else 
                {
                    $reply[] = '*URL:* ' . $result['Id'];  
                }

                $reply[] = '*Published:* ' . date('d/m/Y', strtotime($result['Date'])) . PHP_EOL;
            }
        }
        
        # Need to check for 404 exceptions here, will do that soon
        else
        {
            $reply[] = 'No Leaks found for this email account';
        }

        $bot->reply(implode(PHP_EOL, $reply));
    }
}
