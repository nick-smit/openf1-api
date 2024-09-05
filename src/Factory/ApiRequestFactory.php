<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Factory;

use GuzzleHttp\Client;
use NickSmit\OpenF1Api\Endpoint\AbstractRequest;

readonly class ApiRequestFactory
{
    public function __construct(
        private Client                $client,
        private QueryParameterFactory $queryParameterFactory
    ) {

    }

    public function create(string $class): AbstractRequest
    {
        return new $class($this->client, $this->queryParameterFactory);
    }
}
