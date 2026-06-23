<?php

namespace App\Commands;

use App\Exceptions\PackagistApiException;
use App\Services\PackagistClient;
use LaravelZero\Framework\Commands\Command;

class InfoCommand extends Command
{
    protected $signature = 'info
        {package : Package name (vendor/name)}';

    protected $description = 'Show details about a package published on Packagist';

    public function handle(PackagistClient $client): int
    {
        $name = (string) $this->argument('package');

        try {
            $package = $client->getPackage($name);
        } catch (PackagistApiException $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        }

        if ($package === []) {
            $this->components->warn("Package <comment>{$name}</comment> not found.");

            return self::FAILURE;
        }

        $this->components->twoColumnDetail('Name', (string) ($package['name'] ?? $name));
        $this->components->twoColumnDetail('Description', (string) ($package['description'] ?? '-'));
        $this->components->twoColumnDetail('Repository', (string) ($package['repository'] ?? '-'));
        $this->components->twoColumnDetail('Downloads', (string) ($package['downloads']['total'] ?? 0));
        $this->components->twoColumnDetail('Stars', (string) ($package['github_stars'] ?? 0));
        $this->components->twoColumnDetail('Dependents', (string) ($package['dependents'] ?? 0));

        $versions = $package['versions'] ?? [];
        if (is_array($versions) && $versions !== []) {
            $latest = array_key_first($versions);
            $this->components->twoColumnDetail('Latest version', (string) $latest);
        }

        return self::SUCCESS;
    }
}
