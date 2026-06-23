<?php

namespace App\Commands;

use App\Exceptions\PackagistApiException;
use App\Services\AuthService;
use App\Services\PackagistClient;
use LaravelZero\Framework\Commands\Command;

class ListCommand extends Command
{
    protected $signature = 'list
        {vendor? : Vendor to list (defaults to the authenticated username)}
        {--vendor= : Vendor to list (alternative to the argument)}';

    protected $description = 'List packages published on Packagist by a vendor';

    public function handle(PackagistClient $client, AuthService $auth): int
    {
        $vendor = $this->option('vendor') ?: $this->argument('vendor');

        if (! $vendor) {
            $credentials = $auth->load();
            $vendor = $credentials?->username;
        }

        if (! $vendor) {
            $this->components->error('No vendor given and no authenticated user. Pass a vendor: <comment>packagist list <vendor></comment>.');

            return self::FAILURE;
        }

        try {
            $packages = $client->listByVendor((string) $vendor);
        } catch (PackagistApiException $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        }

        if ($packages === []) {
            $this->components->warn("No packages found for vendor <comment>{$vendor}</comment>.");

            return self::SUCCESS;
        }

        sort($packages);

        $this->table(
            ['Package'],
            array_map(static fn (string $name): array => [$name], $packages),
        );

        $this->components->info(count($packages).' package(s) for <comment>'.$vendor.'</comment>.');

        return self::SUCCESS;
    }
}
