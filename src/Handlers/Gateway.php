<?php

namespace Jeylabs\LaravelAwsLambda\Handlers;

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class Gateway extends Handler
{
    /**
     * Resolve request can handle with handler
     *
     * @return bool
     */
    public function canHandle()
    {
        if (
            array_key_exists('body', $this->payload) &&
            array_key_exists('path', $this->payload) &&
            array_key_exists('headers', $this->payload) &&
            array_key_exists('requestContext', $this->payload) &&
            !array_key_exists('elb', $this->payload['requestContext'])
        ) {
            return true;
        }
        return false;
    }

    /**
     * Handle the request
     *
     * @param Container $app
     * @return false|string
     * @throws BindingResolutionException
     */
    public function handle(Container $app)
    {
        $uri = $this->prepareUrlForRequest($app);
        $request = $this->createRequest($uri);

        if (is_bool(strpos($app->version(), 'Lumen'))) {
            $response = $this->runThroughKernel($app, $request);
        } else {
            $response = $app->prepareResponse(
                $app->handle($request)
            );
        }

        return $this->prepareResponse($response);
    }

    /**
     * Turn the given URI into a fully qualified URL.
     *
     * @param Container $app
     * @return string
     * @throws BindingResolutionException
     */
    protected function prepareUrlForRequest(Container $app)
    {
        $baseUrl = $app->make('config')->get('app.url');
        $uri = $this->payload['path'];

        if (Str::startsWith($uri, '/')) {
            $uri = substr($uri, 1);
        }

        $uri = $baseUrl . '/' . $uri;
        return trim($uri, '/');
    }

    /**
     * Create request from URI
     *
     * @param string $uri
     * @return Request
     */
    protected function createRequest($uri)
    {
        return Request::create(
            $uri, $this->payload['httpMethod'],
            $this->payload['queryStringParameters'] !== null ? $this->payload['queryStringParameters'] : [],
            [], [], $this->transformHeadersToServerVars($this->payload['headers']),
            $this->getBodyFromPayload()
        );
    }

    /**
     * Process the request throw the Http kernel
     *
     * @param Container $app
     * @param $request
     * @return mixed
     * @throws BindingResolutionException
     */
    protected function runThroughKernel(Container $app, $request)
    {
        $kernel = $app->make('Illuminate\Contracts\Http\Kernel');

        $response = $kernel->handle($request);
        $kernel->terminate($request, $response);

        return $response;
    }

    /**
     * Prepare response from
     *
     * @param Response $response
     * @return false|string
     */
    private function prepareResponse(Response $response)
    {
        $payload = [];

        $payload['body'] = $response->getContent();
        $payload['isBase64Encoded'] = false;
        $payload['multiValueHeaders'] = $response->headers->allPreserveCase();
        $payload['statusCode'] = $response->getStatusCode();

        return json_encode($payload);
    }

    /**
     * Get body from payload
     *
     * @return bool|string
     */
    private function getBodyFromPayload()
    {
        if ($this->payload['isBase64Encoded'] === true) {
            return base64_decode($this->payload['body']);
        }

        return $this->payload['body'];
    }

    /**
     * Transform headers array to array of $_SERVER vars with HTTP_* format.
     *
     * @param array $headers
     * @return array
     */
    private function transformHeadersToServerVars(array $headers)
    {
        $server = [];
        $prefix = 'HTTP_';

        foreach ($headers as $name => $value) {
            $name = strtr(strtoupper($name), '-', '_');

            if (!Str::startsWith($name, $prefix) && $name != 'CONTENT_TYPE') {
                $name = $prefix . $name;
            }

            $server[$name] = $value;
        }

        return $server;
    }
}
