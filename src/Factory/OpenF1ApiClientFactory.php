<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Factory;

use GuzzleHttp\Client;
use NickSmit\OpenF1Api\Client\OpenF1ApiClient;

class OpenF1ApiClientFactory
{
    public function create(
        array $guzzleConfig = [],
    ): OpenF1ApiClient {
        $client = new Client(array_merge(['base_uri' => 'https://api.openf1.org/v1'], $guzzleConfig));

        return new OpenF1ApiClient(new ApiRequestFactory($client, new QueryParameterFactory()));
    }
}
