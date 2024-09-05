<?php
declare(strict_types=1);

use NickSmit\OpenF1Api\Factory\OpenF1ApiClientFactory;
use NickSmit\OpenF1Api\Filter\IdFilter;
use NickSmit\OpenF1Api\Filter\NumberFilter;

require_once __DIR__ . '/../vendor/autoload.php';

$factory = new OpenF1ApiClientFactory();

$client = $factory->create();

$response = $client->carData(driverNumber: 1, sessionKey: IdFilter::latest(), speed: NumberFilter::greaterThan(320));

var_dump($response);