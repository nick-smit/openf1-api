<?php
declare(strict_types=1);

use NickSmit\OpenF1Api\Factory\OpenF1ApiClientFactory;

require_once __DIR__ . '/../vendor/autoload.php';

$factory = new OpenF1ApiClientFactory();

$client = $factory->create();

$response = $client->meetings(
    year: 2023,
);

var_dump($response);