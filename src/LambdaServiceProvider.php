<?php

namespace Jeylabs\LaravelAwsLambda;

use Illuminate\Support\ServiceProvider;

class LambdaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/aws-lambda.php' => config_path('aws-lambda.php')
            ], 'aws-lambda-config');

            $this->publishes([
                __DIR__ . '/../handler/handler.php' => base_path('handler.php')
            ], 'aws-lambda-handler');
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/aws-lambda.php', 'aws-lambda');
    }
}
