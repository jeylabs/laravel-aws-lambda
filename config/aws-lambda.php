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

    /*
   |--------------------------------------------------------------------------
   | URL resource prefix
   |--------------------------------------------------------------------------
   |
   | API gateway prefix
   | Resource prefix remove from url
   | Prepare for response without prefix.
   |
   */
    'prefix' => env('RESOURCE_PREFIX')

];
