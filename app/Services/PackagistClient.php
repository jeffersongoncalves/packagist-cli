<?php

namespace App\Services;

use App\DTOs\PackagistCredentials;
use App\Exceptions\NotAuthenticatedException;
use App\Exceptions\PackagistApiException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

class PackagistClient
{
    private const BASE_URL = 'https://packagist.org';

    public function __construct(
        private readonly AuthService $auth,
        private readonly Client $client = new Client(['timeout' => 30]),
    ) {}

    /**
     * Register a new package on Packagist from its VCS repository URL.
     *
     * @return array<string, mixed>
     */
    public function createPackage(string $repositoryUrl): array
    {
        return $this->authenticatedPost('/api/create-package', $repositoryUrl);
    }

    /**
     * Force Packagist to re-crawl an existing package from its repository URL.
     *
     * @return array<string, mixed>
     */
    public function updatePackage(string $repositoryUrl): array
    {
        return $this->authenticatedPost('/api/update-package', $repositoryUrl);
    }

    /**
     * List package names for a vendor.
     *
     * @return list<string>
     */
    public function listByVendor(string $vendor): array
    {
        $data = $this->get('/packages/list.json', ['vendor' => $vendor]);

        /** @var list<string> $names */
        $names = $data['packageNames'] ?? [];

        return $names;
    }

    /**
     * Fetch package metadata.
     *
     * @return array<string, mixed>
     */
    public function getPackage(string $name): array
    {
        $data = $this->get('/packages/'.$name.'.json');

        /** @var array<string, mixed> $package */
        $package = $data['package'] ?? [];

        return $package;
    }

    /**
     * @return array<string, mixed>
     */
    private function authenticatedPost(string $path, string $repositoryUrl): array
    {
        $credentials = $this->auth->load();

        if (! $credentials instanceof PackagistCredentials) {
            throw new NotAuthenticatedException;
        }

        $url = self::BASE_URL.$path.'?'.http_build_query([
            'username' => $credentials->username,
            'apiToken' => $credentials->apiToken,
        ]);

        try {
            $response = $this->client->post($url, [
                'headers' => ['Content-Type' => 'application/json'],
                'body' => json_encode([
                    'repository' => ['url' => $repositoryUrl],
                ], JSON_UNESCAPED_SLASHES),
            ]);
        } catch (ClientException $e) {
            $body = json_decode((string) $e->getResponse()->getBody(), true);

            throw PackagistApiException::fromResponse(
                $e->getResponse()->getStatusCode(),
                is_array($body) ? $body : []
            );
        } catch (GuzzleException $e) {
            throw new PackagistApiException('Packagist request failed: '.self::redact($e->getMessage()));
        }

        $data = json_decode((string) $response->getBody(), true);

        return is_array($data) ? $data : [];
    }

    /**
     * @param  array<string, string>  $query
     * @return array<string, mixed>
     */
    private function get(string $path, array $query = []): array
    {
        $url = self::BASE_URL.$path;

        if ($query !== []) {
            $url .= '?'.http_build_query($query);
        }

        try {
            $response = $this->client->get($url, [
                'headers' => ['Accept' => 'application/json'],
            ]);
        } catch (ClientException $e) {
            $body = json_decode((string) $e->getResponse()->getBody(), true);

            throw PackagistApiException::fromResponse(
                $e->getResponse()->getStatusCode(),
                is_array($body) ? $body : []
            );
        } catch (GuzzleException $e) {
            throw new PackagistApiException('Packagist request failed: '.self::redact($e->getMessage()));
        }

        $data = json_decode((string) $response->getBody(), true);

        return is_array($data) ? $data : [];
    }

    /**
     * Mask the apiToken query parameter so credentials never leak into error messages.
     */
    private static function redact(string $message): string
    {
        return (string) preg_replace('/(apiToken=)[^&\s]+/i', '$1***', $message);
    }
}
