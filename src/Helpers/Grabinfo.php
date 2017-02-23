<?php

namespace Pillus\Slackbot\Helpers;

use GuzzleHttp\Client;

class Grabinfo
{
    public function grab($data, $method)
    {
        $client = new Client($data);

        try 
        {
            $response = $client->request($method);

            if (is_bool($response))
            {
            return $response;
            }

            else
            {
            return json_decode($response->getBody()->getContents(), true);
            }
        } 

        catch (\GuzzleHttp\Exception\ClientException $e) 
        {
            return false;
        }

        catch (\GuzzleHttp\Exception\RequestException $e) 
        {
            return false;
        }
    }
}

        