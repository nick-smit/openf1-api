<?php
declare(strict_types=1);

namespace Tests\Unit\Factory;

use NickSmit\OpenF1Api\Client\OpenF1ApiClient;
use NickSmit\OpenF1Api\Factory\OpenF1ApiClientFactory;
use PHPUnit\Framework\TestCase;

class OpenF1ApiClientFactoryTest extends TestCase
{
    public function test_the_open_f1_api_client_can_be_created(): void
    {
        $factory = new OpenF1ApiClientFactory();

        $client = $factory->create();

        self::assertInstanceOf(OpenF1ApiClient::class, $client);
    }
}