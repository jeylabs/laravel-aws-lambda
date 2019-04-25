<?php

namespace Jeylabs\LaravelAwsLambda\Handlers;

use Illuminate\Contracts\Console\Kernel;

class Artisan extends Handler
{
    /**
     * Resolve request can handle with handler
     *
     * @return bool
     */
    public function canHandle()
    {
        return array_key_exists('command', $this->payload);
    }

    /**
     * Handle the request
     *
     * @param Kernel $kernel
     * @return int
     */
    public function handle(Kernel $kernel)
    {
        $result = $kernel->call($this->payload['command']);

        $kernel->terminate(null, $result);

        return $result;
    }
}
