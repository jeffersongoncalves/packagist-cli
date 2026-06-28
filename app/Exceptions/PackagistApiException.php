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

        $message = self::stringify($message);

        if (isset($body['details'])) {
            $message .= ' '.self::stringify($body['details']);
        }

        return new self($message, $statusCode, $body);
    }

    /**
     * Flatten an API payload value (string or array) into a readable message.
     *
     * @param  mixed  $value
     */
    private static function stringify($value): string
    {
        if (is_array($value)) {
            return (string) json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        return (string) $value;
    }
}
