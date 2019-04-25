<?php

namespace Jeylabs\LaravelAwsLambda\Handlers;

use Illuminate\Foundation\Application;
use Illuminate\Queue\Worker;
use Illuminate\Queue\WorkerOptions;
use Jeylabs\LaravelAwsLambda\Queue\Jobs\Sqs as SqsJob;
use Throwable;

class Sqs extends Handler
{
    /**
     * Resolve request can handle with handler
     *
     * @return bool
     */
    public function canHandle()
    {
        return array_key_exists('eventSource', $this->payload) && $this->payload['eventSource'] == 'aws:sqs';
    }

    /**
     * Handle the request
     *
     * @param Application $app
     * @param Worker $worker
     * @throws Throwable
     */
    public function handle(Application $app, Worker $worker)
    {
        $job = new SqsJob($app, $this->payload);
        $worker->process('lambda', $job, new WorkerOptions());
    }
}
