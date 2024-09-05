<?php

declare(strict_types=1);

use NickSmit\OpenF1Api\Factory\OpenF1ApiClientFactory;
use NickSmit\OpenF1Api\Filter\IdFilter;
use NickSmit\OpenF1Api\Filter\NumberFilter;

require_once __DIR__ . '/../vendor/autoload.php';

$factory = new OpenF1ApiClientFactory();

$client = $factory->create();

$response = $client->position(
    driverNumber: 40,
    meetingKey: IdFilter::id(1217),
    position: NumberFilter::lessThan(3),
);

var_dump($response);
