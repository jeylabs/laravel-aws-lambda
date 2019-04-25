<?php

namespace Jeylabs\LaravelAwsLambda\Contracts;

interface Handler
{
    /**
     * Resolve request can handle with handler
     *
     * @return bool
     */
    public function canHandle();

    /**
     * Setter of payload property
     *
     * @param $payload
     */
    public function setPayload($payload);
}
