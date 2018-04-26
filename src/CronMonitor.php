<?php

namespace Omnispear\CronMonitor;

use Carbon\Carbon;
use GuzzleHttp\Client;

class CronMonitor
{
    private $client;
    private $config;

    public function __construct()
    {
        $this->config = include('config.php');

        $this->client = new Client([
            'base_uri' => $this->config['CRONMONITOR_API'],
        ]);
    }

    /**
     * Notify API an execution has started
     */
    public function sendStarted($environment, $task_uuid)
    {
        $response = json_decode($this->client->request('POST', 'started', [
            'form_params' => [
                'task_id' => $task_uuid,
                'environment' => $environment,
                'started' => Carbon::now()->toDateTimeString()
            ]
        ])->getBody()->getContents());

        return $response->status === 200 ? $response->uuid : $response;
    }

    /**
     * Notify API an execution has ended
     */
    public function sendEnded($execution_uuid)
    {
        $this->client->request('POST', 'ended', [
            'form_params' => [
                'execution_uuid' => $execution_uuid,
                'ended' => Carbon::now()->toDateTimeString()
            ]
        ]);
    }

    /**
     * Notify API an execution has resulted in error
     */
    public function sendError($execution_uuid, $error)
    {
        $this->client->request('POST', 'error', [
            'form_params' => [
                'execution_id' => $execution_uuid,
                'error' => $error,
                'ended' => Carbon::now()->toDateTimeString()
            ]
        ]);
    }
}