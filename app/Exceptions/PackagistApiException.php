<?php

namespace App\Exceptions;

use RuntimeException;

class PackagistApiException extends RuntimeException
{
    /**
     * @param  array<string, mixed>|null  $response
     */
    public function __construct(
        string $message,
        public readonly int $statusCode = 0,
        public readonly ?array $response = null,
    ) {
        parent::__construct($message);
    }

    /**
     * @param  array<string, mixed>  $body
     */
    public static function fromResponse(int $statusCode, array $body): self
    {
        $message = $body['message']
            ?? $body['error']
            ?? ($statusCode === 404 ? 'Package not found.' : 'Packagist API request failed.');

        if (isset($body['details']) && is_string($body['details'])) {
            $message .= ' '.$body['details'];
        }

        return new self((string) $message, $statusCode, $body);
    }
}
