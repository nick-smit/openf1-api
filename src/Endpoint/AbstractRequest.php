<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Endpoint;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use JsonException;
use NickSmit\OpenF1Api\Exception\ApiUnavailableException;
use NickSmit\OpenF1Api\Exception\UnexpectedResponseException;
use NickSmit\OpenF1Api\Factory\QueryParameterFactory;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractRequest
{
    public function __construct(
        protected readonly Client                $client,
        protected readonly QueryParameterFactory $queryParameterFactory
    ) {
    }

    /**
     * @throws UnexpectedResponseException
     * @throws ApiUnavailableException
     */
    protected function executeRequest(string $endpoint, array $queryParams)
    {
        $response = $this->getResponse($endpoint, $queryParams);

        $decodedResponse = $this->decodeResponse($response);

        $this->assertResponseIsArray($decodedResponse, $response);

        return $decodedResponse;
    }

    /**
     * @throws ApiUnavailableException
     */
    private function getResponse(string $uri, array $queryParams): ResponseInterface
    {
        $queryParams = array_filter($queryParams);

        // Custom query creation as PHP always encodes greater than and less than characters in parameter keys
        $query = '';
        foreach ($queryParams as $key => $value) {
            if ($query !== '') {
                $query .= '&';
            }

            $query .= $key . '=' . urlencode((string)$value);
        }

        try {
            return $this->client->get($uri, [
                RequestOptions::QUERY => $query,
            ]);
        } catch (GuzzleException $guzzleException) {
            throw new ApiUnavailableException($guzzleException->getMessage(), previous: $guzzleException);
        }
    }

    /**
     * @throws UnexpectedResponseException
     */
    private function decodeResponse(ResponseInterface $response): mixed
    {
        try {
            return json_decode($response->getBody()->getContents(), true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $jsonException) {
            throw new UnexpectedResponseException('Invalid json response', $response, previous: $jsonException);
        }
    }

    /**
     * @throws UnexpectedResponseException
     */
    private function assertResponseIsArray(mixed $decodedResponse, ResponseInterface $response): void
    {
        if (!is_array($decodedResponse)) {
            throw new UnexpectedResponseException('Array response was expected', $response);
        }
    }
}
