<?php

use App\DTOs\PackagistCredentials;
use App\Services\AuthService;

beforeEach(function () {
    $this->tmp = sys_get_temp_dir().'/packagist-cli-test-'.uniqid();
    putenv('XDG_CONFIG_HOME='.$this->tmp);
    $this->auth = new AuthService;
});

afterEach(function () {
    @unlink($this->tmp.'/packagist.json');
    @rmdir($this->tmp);
    putenv('XDG_CONFIG_HOME');
});

it('stores credentials under the config dir', function () {
    expect($this->auth->getConfigPath())->toBe($this->tmp.DIRECTORY_SEPARATOR.'packagist.json');
});

it('saves and loads credentials roundtrip', function () {
    $this->auth->save(new PackagistCredentials('jeff', 'secret-token'));

    $loaded = $this->auth->load();

    expect($loaded)->toBeInstanceOf(PackagistCredentials::class)
        ->and($loaded->username)->toBe('jeff')
        ->and($loaded->apiToken)->toBe('secret-token');
});

it('reports authenticated state', function () {
    expect($this->auth->isAuthenticated())->toBeFalse();

    $this->auth->save(new PackagistCredentials('jeff', 'token'));

    expect((new AuthService)->isAuthenticated())->toBeTrue();
});

it('forgets credentials', function () {
    $this->auth->save(new PackagistCredentials('jeff', 'token'));
    $this->auth->forget();

    expect($this->auth->load())->toBeNull();
});

it('returns null for invalid stored data', function () {
    @mkdir($this->tmp, 0700, true);
    file_put_contents($this->auth->getConfigPath(), json_encode(['username' => '']));

    expect($this->auth->load())->toBeNull();
});
