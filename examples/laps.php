<?php
declare(strict_types=1);

use NickSmit\OpenF1Api\Factory\OpenF1ApiClientFactory;
use NickSmit\OpenF1Api\Filter\IdFilter;

require_once __DIR__ . '/../vendor/autoload.php';

$factory = new OpenF1ApiClientFactory();

$client = $factory->create();

$response = $client->laps(
    driverNumber: 63,
    lapNumber: 8,
    sessionKey: IdFilter::id(9161)
);

var_dump($response);