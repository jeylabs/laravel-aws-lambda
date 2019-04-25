<?php

namespace Jeylabs\LaravelAwsLambda\Handlers;

use Jeylabs\LaravelAwsLambda\Contracts\Handler as HandlerContract;

abstract class Handler implements HandlerContract
{
    /**
     *  Request payload
     *
     * @var array
     */
    protected $payload;

    /**
     * Setter of payload property
     *
     * @param $payload
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;
    }
}
