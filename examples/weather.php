<?php
declare(strict_types=1);

use NickSmit\OpenF1Api\Factory\OpenF1ApiClientFactory;
use NickSmit\OpenF1Api\Filter\IdFilter;

require_once __DIR__ . '/../vendor/autoload.php';

$factory = new OpenF1ApiClientFactory();

$client = $factory->create();

$response = $client->weather(
    sessionKey: IdFilter::latest(),
);

var_dump($response);