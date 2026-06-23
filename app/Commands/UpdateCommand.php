<?php

namespace App\Commands;

use App\Exceptions\NotAuthenticatedException;
use App\Exceptions\PackagistApiException;
use App\Services\PackagistClient;
use App\Services\RepositoryResolver;
use LaravelZero\Framework\Commands\Command;

class UpdateCommand extends Command
{
    protected $signature = 'update
        {package : Package name (vendor/name) or repository URL}';

    protected $description = 'Force Packagist to re-crawl an existing package';

    public function handle(PackagistClient $client, RepositoryResolver $resolver): int
    {
        $input = (string) $this->argument('package');

        $url = $this->resolveRepositoryUrl($client, $resolver, $input);

        $this->components->info("Requesting update for <comment>{$url}</comment>...");

        try {
            $response = $client->updatePackage($url);
        } catch (NotAuthenticatedException $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        } catch (PackagistApiException $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        }

        $status = $response['status'] ?? 'success';
        $this->components->info("Update requested (<comment>{$status}</comment>).");

        return self::SUCCESS;
    }

    private function resolveRepositoryUrl(PackagistClient $client, RepositoryResolver $resolver, string $input): string
    {
        if (! $resolver->isPackageName($input)) {
            return $resolver->normalizeUrl($input);
        }

        try {
            $package = $client->getPackage($input);
            $repository = $package['repository'] ?? null;

            if (is_string($repository) && $repository !== '') {
                return $repository;
            }
        } catch (PackagistApiException) {
            // Fall back to assuming a GitHub repository below.
        }

        return $resolver->normalizeUrl($input);
    }
}
