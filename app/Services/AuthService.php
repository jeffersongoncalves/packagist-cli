<?php

namespace App\Services;

use App\DTOs\PackagistCredentials;

class AuthService
{
    private ?PackagistCredentials $credentials = null;

    public function save(PackagistCredentials $credentials): void
    {
        $dir = $this->getConfigDir();

        if (! is_dir($dir)) {
            @mkdir($dir, 0700, true);
        }

        $path = $this->getConfigPath();

        file_put_contents(
            $path,
            json_encode($credentials->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL
        );

        @chmod($path, 0600);

        $this->credentials = $credentials;
    }

    public function load(): ?PackagistCredentials
    {
        if ($this->credentials instanceof PackagistCredentials) {
            return $this->credentials;
        }

        $path = $this->getConfigPath();

        if (! is_file($path)) {
            return null;
        }

        $contents = @file_get_contents($path);

        if ($contents === false) {
            return null;
        }

        $data = json_decode($contents, true);

        if (! is_array($data)) {
            return null;
        }

        $credentials = PackagistCredentials::fromArray($data);

        if (! $credentials->isValid()) {
            return null;
        }

        return $this->credentials = $credentials;
    }

    public function isAuthenticated(): bool
    {
        return $this->load() instanceof PackagistCredentials;
    }

    public function forget(): void
    {
        $path = $this->getConfigPath();

        if (is_file($path)) {
            @unlink($path);
        }

        $this->credentials = null;
    }

    public function getConfigPath(): string
    {
        return $this->getConfigDir().DIRECTORY_SEPARATOR.'packagist.json';
    }

    public function getConfigDir(): string
    {
        $xdg = getenv('XDG_CONFIG_HOME');

        if (is_string($xdg) && $xdg !== '') {
            return rtrim($xdg, '/\\');
        }

        return $this->getHomeDir().DIRECTORY_SEPARATOR.'.config';
    }

    public function getHomeDir(): string
    {
        $home = getenv('HOME');

        if (is_string($home) && $home !== '') {
            return rtrim($home, '/\\');
        }

        $userProfile = getenv('USERPROFILE');

        if (is_string($userProfile) && $userProfile !== '') {
            return rtrim($userProfile, '/\\');
        }

        $drive = getenv('HOMEDRIVE');
        $path = getenv('HOMEPATH');

        if (is_string($drive) && is_string($path) && $drive !== '' && $path !== '') {
            return rtrim($drive.$path, '/\\');
        }

        return getcwd() ?: '.';
    }
}
