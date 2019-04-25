<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Handlers
    |--------------------------------------------------------------------------
    |
    | Register handlers to handle Lambda requests
    | Each handlers implementing Handler contracts
    | And each Handler's handle method invoke when handle.
    |
    */
    'handlers' => [
        \Jeylabs\LaravelAwsLambda\Handlers\Sqs::class,
        \Jeylabs\LaravelAwsLambda\Handlers\Artisan::class,
        \Jeylabs\LaravelAwsLambda\Handlers\Gateway::class,
    ],

];
