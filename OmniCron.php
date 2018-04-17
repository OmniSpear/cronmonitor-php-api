<?php

namespace OmniCron;

use Carbon\Carbon;
use GuzzleHttp\Client;

require "vendor/nesbot/carbon/src/Carbon/Carbon.php";
require "vendor/guzzlehttp/guzzle/src/Client.php";

class OmniCron
{
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => getenv('OMNICRON_API'),
        ]);
    }

    /**
     * Notify API an execution has started
     */
    public function sendStarted($environment, $task_id)
    {
        $execution_id = $this->client->request('POST', 'started' ,[
            'form_params' => [
                'task_id' => $task_id,
                'environment' => $environment,
                'started' => Carbon::now()->toDateTimeString()
            ]
        ])->getBody()->getContents();

        return $execution_id;
    }

    /**
     * Notify API an execution has ended
     */
    public function sendEnded($execution_id)
    {
        $this->client->request('POST', 'ended' ,[
            'form_params' => [
                'execution_id' => $execution_id,
                'ended' => Carbon::now()->toDateTimeString()
            ]
        ])->getBody()->getContents();
    }

    /**
     * Notify API an execution has resulted in error
     */
    public function sendError($execution_id, $error)
    {
        $this->client->request('POST', 'error' ,[
            'form_params' => [
                'execution_id' => $execution_id,
                'error' => $error,
                'ended' => Carbon::now()->toDateTimeString()
            ]
        ])->getBody()->getContents();
    }

}