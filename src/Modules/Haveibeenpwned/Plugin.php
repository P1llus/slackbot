<?php

namespace Pillus\Slackbot\Modules\Haveibeenpwned;

use Pillus\Slackbot\Modules\Haveibeenpwned\Haveibeenpwned;

/**
* This module checks haveibeenpwned.com for leaks related to the choosen account
* or email address, and returns with the leaked information
**/

class Plugin
{

    /**
     * @var Botman
     */
    protected $botman;

    /**
     * @var Haveibeenpwned
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

    /**
    * Initiates all available commands used by the Shodan module.
    **/

    public function init()
    {
        $this->botman->hears('!haveibeenpwned account {account}', self::class.'@handleAccountSearch');
        $this->botman->hears('!haveibeenpwned email {email}', self::class.'@handleEmailPasteSearch');
    }

    /**
    * Searches a specific username or account name for leaked information
    **/

    public function handleAccountSearch($bot, $account)
    {
        $results = $this->service->AccountSearch($account);
        
        # Sanity check for the returning response from the API.
        if (is_bool($results) || count($results) === 0) {
            $reply[] = sprintf('*No Leaks found for this account*');
            $bot->reply(implode(PHP_EOL, $reply));
            return;
        }

        # Get the title and date of the leak, and confirms if it is verified
        else {
            foreach ($results as $result) {
                $title = array_get($result, 'Title');
                $date = array_get($result, 'BreachDate');
                $verified = array_get($result, 'IsVerified');

                $reply[] = sprintf('Title: %s', $title);
                $reply[] = sprintf('Breach Date: %s', $date);
                $reply[] = sprintf('Verified leak?: %s', $verified ? 'yes':'no') . PHP_EOL;
            }

            $bot->reply(implode(PHP_EOL, $reply));
            return;
        }
    }

    /**
    * Lists all sources a email address is found in different database breaches
    **/
    public function handleEmailPasteSearch($bot, $email)
    {
        $results = $this->service->emailPasteSearch($email);

        # Sanity check for the returning response from the API.
        if (is_bool($results) || count($results) === 0) {
            $reply[] = sprintf('No Leaks found for this email account');
            $bot->reply(implode(PHP_EOL, $reply));
            return;
        } else {
            $reply[] = sprintf('Your email has been leaked %s Times', count($results));

            foreach ($results as $result) {
                $title = array_get($result, 'Title');
                $source = array_get($result, 'Source');
                $resultId = array_get($result, 'Id');
                $date = date('d/m/Y', strtotime(array_get($result, 'Date')));

                if (count(array_get($result, 'Title')) > 0) {
                    $reply[] = sprintf('*Title:* %s', $title);
                } else {
                    $reply[] = sprintf('*Title:* No title found');
                }
                    
                $reply[] = sprintf('*Source:* %s', $source);

                if ($source == 'Pastebin') {
                    $reply[] = sprintf('*URL:* http://www.pastebin.com/%s', $resultId) . PHP_EOL;
                } else {
                    $reply[] = sprintf('*URL:* %s', $resultId);
                }

                $reply[] = sprintf('*Published:* %s', $date) . PHP_EOL;
            }
            $bot->reply(implode(PHP_EOL, $reply));
            return;
        }
    }
}
