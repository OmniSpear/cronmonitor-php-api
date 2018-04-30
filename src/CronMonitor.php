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
    public function sendStarted($task_uuid, $environment)
    {
        $response = json_decode($this->client->request('POST', $task_uuid . '/started', [
            'form_params' => [
                'environment' => $environment,
                'start_time' => Carbon::now()->toDateTimeString()
            ]
        ])->getBody()->getContents());

        return $response->success ? $response->data->uuid : $response;
    }

    /**
     * Notify API an execution has ended
     */
    public function sendEnded($execution_uuid)
    {
        $response = json_decode($this->client->request('POST', $execution_uuid . '/ended', [
            'form_params' => [
                'end_time' => Carbon::now()->toDateTimeString()
            ]
        ])->getBody()->getContents());

        return $response->success ?: $response;
    }

    /**
     * Notify API an execution has resulted in error
     */
    public function sendError($execution_uuid, $error)
    {
        $response = json_decode($this->client->request('POST', 'error', [
            'form_params' => [
                'execution_id' => $execution_uuid,
                'error' => $error,
                'end_time' => Carbon::now()->toDateTimeString()
            ]
        ])->getBody()->getContents());

        return $response->success ?: $response;
    }
}