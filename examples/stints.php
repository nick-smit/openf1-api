<?php

declare(strict_types=1);

use NickSmit\OpenF1Api\Factory\OpenF1ApiClientFactory;

require_once __DIR__ . '/../vendor/autoload.php';

$factory = new OpenF1ApiClientFactory();

$client = $factory->create();

$response = $client->stints();

var_dump($response);
