<?php

namespace Jeylabs\LaravelAwsLambda;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Collection;

class Executor
{
    /**
     * Registered handlers
     *
     * @var Collection
     */
    protected $handlers;

    /**
     * Application instance
     *
     * @var Container
     */
    protected $app;

    /**
     * Create executor instance.
     *
     * @param Container $app
     * @throws BindingResolutionException
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
        $this->handlers = $app->make('config')->get('aws-lambda.handlers');
    }

    /**
     * Handle the request
     *
     * @param $payload
     * @return mixed|void
     * @throws Exception
     */
    public function handle($payload)
    {
        if (array_key_exists('Records', $payload)) {
            $records = collect($payload['Records']);
            foreach ($records as $record) {
                $this->runHandlers($record);
            }
        } else {
            return $this->runHandlers($payload);
        }
    }

    /**
     * Handle request with Registered handlers
     *
     * @param $payload
     * @return mixed
     * @throws Exception
     */
    private function runHandlers($payload)
    {
        foreach ($this->handlers as $handler) {
            $instance = $this->app->make($handler);
            $instance->setPayload($payload);

            if ($instance->canHandle()) {
                return $this->app->call([$instance, 'handle']);
            }
        }

        throw new Exception('No valid handler found for message');
    }
}
