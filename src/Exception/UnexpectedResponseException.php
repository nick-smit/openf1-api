<?php

declare(strict_types=1);

namespace NickSmit\OpenF1Api\Exception;

use GuzzleHttp\Psr7\Response;
use Throwable;

class UnexpectedResponseException extends OpenF1ApiException
{
    public function __construct(string $message, public readonly ?Response $response = null, ?Throwable $previous = null)
    {
        parent::__construct($message, $this->response?->getStatusCode() ?? 0, $previous);
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }
}
