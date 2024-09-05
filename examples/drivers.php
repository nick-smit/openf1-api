<?php

declare(strict_types=1);


require_once __DIR__ . '/../vendor/autoload.php';

$factory = new NickSmit\OpenF1Api\Factory\OpenF1ApiClientFactory();
$apiClient = $factory->create();

// Retrieve the drivers participating in the latest session.
$drivers = $apiClient->drivers(sessionKey: NickSmit\OpenF1Api\Filter\IdFilter::latest());

foreach ($drivers as $driver) {
    echo $driver->fullName . "\n";
}
