<?php

namespace App\DTOs;

class PackagistCredentials
{
    public function __construct(
        public readonly string $username,
        public readonly string $apiToken,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            username: (string) ($data['username'] ?? ''),
            apiToken: (string) ($data['apiToken'] ?? ''),
        );
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'username' => $this->username,
            'apiToken' => $this->apiToken,
        ];
    }

    public function isValid(): bool
    {
        return $this->username !== '' && $this->apiToken !== '';
    }
}
