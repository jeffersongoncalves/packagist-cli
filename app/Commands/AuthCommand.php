<?php

namespace App\Commands;

use App\DTOs\PackagistCredentials;
use App\Services\AuthService;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class AuthCommand extends Command
{
    protected $signature = 'auth
        {--username= : Your Packagist username}
        {--token= : Your Packagist API token}
        {--show : Show the currently stored credentials}
        {--forget : Remove the stored credentials}';

    protected $description = 'Authenticate with Packagist and store the API token locally';

    public function handle(AuthService $auth): int
    {
        if ($this->option('forget')) {
            $auth->forget();
            $this->components->info('Stored Packagist credentials removed.');

            return self::SUCCESS;
        }

        if ($this->option('show')) {
            $credentials = $auth->load();

            if (! $credentials instanceof PackagistCredentials) {
                $this->components->warn('No Packagist credentials stored. Run <comment>packagist auth</comment> to add them.');

                return self::FAILURE;
            }

            $this->components->twoColumnDetail('Config file', $auth->getConfigPath());
            $this->components->twoColumnDetail('Username', $credentials->username);
            $this->components->twoColumnDetail('API token', $this->mask($credentials->apiToken));

            return self::SUCCESS;
        }

        $username = $this->option('username') ?: text(
            label: 'Packagist username',
            required: true,
        );

        $token = $this->option('token') ?: password(
            label: 'Packagist API token',
            hint: 'Find it at https://packagist.org/profile/',
            required: true,
        );

        $credentials = new PackagistCredentials((string) $username, (string) $token);

        $auth->save($credentials);

        $this->components->info('Packagist credentials saved to <comment>'.$auth->getConfigPath().'</comment>.');

        return self::SUCCESS;
    }

    private function mask(string $token): string
    {
        $length = mb_strlen($token);

        if ($length <= 4) {
            return str_repeat('*', $length);
        }

        return str_repeat('*', $length - 4).mb_substr($token, -4);
    }
}
