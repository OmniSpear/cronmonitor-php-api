<?php

namespace Omnispear\CronMonitor;

use Carbon\Carbon;
use GuzzleHttp\Client;

class CronMonitor
{
    /**
     * @var Client : GuzzleHttp client
     */
    private $client;

    /**
     * @var mixed : Configuration settings
     */
    private $config;

    /**
     * CronMonitor constructor
     */
    public function __construct()
    {
        $this->config = include('config.php');

        $this->client = new Client([
            'base_uri' => $this->config['CRONMONITOR_API'],
        ]);
    }

    /**
     * Notify API an execution has started
     *
     * @param $task_uuid : Unique identifier for task
     * @param $environment : Environment task is running in
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendStarted($task_uuid, $environment)
    {
        $response = json_decode($this->client->request('POST', "{$task_uuid}/started", [
            'form_params' => [
                'environment' => $environment,
                'start_time' => Carbon::now()->toDateTimeString()
            ]
        ])->getBody()->getContents());

        return $response->success ? $response->data->id : $response;
    }

    /**
     * Notify API an execution has ended
     *
     * @param $execution_id : ID of task execution
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendEnded($execution_id)
    {
        $response = json_decode($this->client->request('POST', "{$execution_id}/ended", [
            'form_params' => [
                'end_time' => Carbon::now()->toDateTimeString()
            ]
        ])->getBody()->getContents());

        return $response->success ?: $response;
    }

    /**
     * Notify API an execution has resulted in error
     *
     * @param $execution_id : ID of task execution
     * @param $error : Error text of throw exception
     * @return
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendError($execution_id, $error)
    {
        $response = json_decode($this->client->request('POST', 'error', [
            'form_params' => [
                'execution_id' => $execution_id,
                'error' => $error,
                'end_time' => Carbon::now()->toDateTimeString()
            ]
        ])->getBody()->getContents());

        return $response->success ?: $response->errors;
    }
}