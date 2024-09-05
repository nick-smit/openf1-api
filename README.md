# OpenF1 Api

An API client for [openf1.org](https://openf1.org) using [guzzlehttp/guzzle](https://github.com/guzzle/guzzle).

## Installation

You can add this library as a dependency to your project using [Composer](https://getcomposer.org):

    composer require nick-smit/openf1-api

## Usage example

```php
$factory = new NickSmit\OpenF1Api\Factory\OpenF1ApiClientFactory();
$apiClient = $factory->create();

// Retrieve the drivers who participated in the latest session.
$drivers = $apiClient->drivers(sessionKey: NickSmit\OpenF1Api\Filter\IdFilter::latest());

foreach ($drivers as $driver) {
    echo $driver->fullName . "\n";
}
```

You will find more examples in the [examples](examples) directory.

## License

OpenF1 Api is made available under the MIT License (MIT). Please see the [License File](LICENSE) for more information.

## Disclaimer

OpenF1 is an unofficial project and is not associated in any way with the Formula 1 companies.
F1, FORMULA ONE, FORMULA 1, FIA FORMULA ONE WORLD CHAMPIONSHIP, GRAND PRIX and related marks are trade marks of Formula One Licensing B.V.
