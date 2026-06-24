<?php

namespace App\Commands;

use App\Exceptions\PackagistApiException;
use App\Services\PackagistClient;
use LaravelZero\Framework\Commands\Command;

class ShowCommand extends Command
{
    protected $signature = 'show
        {package : Package name (vendor/name)}
        {--limit=0 : Limit the number of versions shown (0 = all)}';

    protected $description = 'List every version published for a package on Packagist';

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

        $versions = $package['versions'] ?? [];

        if (! is_array($versions) || $versions === []) {
            $this->components->warn("No versions published for <comment>{$name}</comment>.");

            return self::SUCCESS;
        }

        $limit = (int) $this->option('limit');
        $rows = [];

        foreach ($versions as $key => $version) {
            $version = is_array($version) ? $version : [];

            $rows[] = [
                (string) ($version['version'] ?? $key),
                $this->formatTime((string) ($version['time'] ?? '')),
                (string) ($version['require']['php'] ?? '-'),
                $this->formatLicense($version['license'] ?? null),
            ];

            if ($limit > 0 && count($rows) >= $limit) {
                break;
            }
        }

        $this->table(['Version', 'Released', 'PHP', 'License'], $rows);

        $shown = count($rows);
        $total = count($versions);
        $suffix = $shown < $total ? " (of {$total})" : '';

        $this->components->info($shown.' version(s)'.$suffix.' for <comment>'.$name.'</comment>.');

        return self::SUCCESS;
    }

    private function formatTime(string $time): string
    {
        if ($time === '') {
            return '-';
        }

        $timestamp = strtotime($time);

        return $timestamp === false ? $time : date('Y-m-d', $timestamp);
    }

    /**
     * @param  mixed  $license
     */
    private function formatLicense($license): string
    {
        if (is_array($license)) {
            return $license === [] ? '-' : implode(', ', array_map('strval', $license));
        }

        return $license === null ? '-' : (string) $license;
    }
}
