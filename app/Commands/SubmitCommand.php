<?php

namespace App\Commands;

use App\Exceptions\NotAuthenticatedException;
use App\Exceptions\PackagistApiException;
use App\Services\PackagistClient;
use App\Services\RepositoryResolver;
use LaravelZero\Framework\Commands\Command;

class SubmitCommand extends Command
{
    protected $signature = 'submit
        {repository : Repository URL or owner/repo shorthand (assumed GitHub)}';

    protected $description = 'Register a new package on Packagist from its repository URL';

    public function handle(PackagistClient $client, RepositoryResolver $resolver): int
    {
        $url = $resolver->normalizeUrl((string) $this->argument('repository'));

        $this->components->info("Submitting <comment>{$url}</comment> to Packagist...");

        try {
            $response = $client->createPackage($url);
        } catch (NotAuthenticatedException $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        } catch (PackagistApiException $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        }

        $status = $response['status'] ?? 'success';
        $this->components->info("Package submitted (<comment>{$status}</comment>).");

        return self::SUCCESS;
    }
}
