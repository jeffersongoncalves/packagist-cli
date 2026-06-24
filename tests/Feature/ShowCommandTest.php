<?php

use App\Services\PackagistClient;

it('lists every version of a package', function () {
    $client = Mockery::mock(PackagistClient::class);
    $client->shouldReceive('getPackage')
        ->once()
        ->with('vendor/pkg')
        ->andReturn([
            'name' => 'vendor/pkg',
            'description' => 'A package',
            'repository' => 'https://github.com/vendor/pkg',
            'versions' => [
                '2.0.0' => ['version' => '2.0.0', 'time' => '2026-02-01T10:00:00+00:00', 'require' => ['php' => '^8.2'], 'license' => ['MIT']],
                '1.0.0' => ['version' => '1.0.0', 'time' => '2026-01-01T10:00:00+00:00', 'require' => ['php' => '^8.1'], 'license' => ['MIT']],
            ],
        ]);

    $this->app->instance(PackagistClient::class, $client);

    $this->artisan('show vendor/pkg')
        ->expectsOutputToContain('2.0.0')
        ->expectsOutputToContain('1.0.0')
        ->expectsOutputToContain('2 version(s)')
        ->assertExitCode(0);
});

it('respects the limit option', function () {
    $client = Mockery::mock(PackagistClient::class);
    $client->shouldReceive('getPackage')->once()->andReturn([
        'name' => 'vendor/pkg',
        'versions' => [
            '2.0.0' => ['version' => '2.0.0'],
            '1.0.0' => ['version' => '1.0.0'],
        ],
    ]);

    $this->app->instance(PackagistClient::class, $client);

    $this->artisan('show vendor/pkg --limit=1')
        ->expectsOutputToContain('1 version(s) (of 2)')
        ->assertExitCode(0);
});

it('warns when the package is not found', function () {
    $client = Mockery::mock(PackagistClient::class);
    $client->shouldReceive('getPackage')->once()->andReturn([]);

    $this->app->instance(PackagistClient::class, $client);

    $this->artisan('show vendor/missing')
        ->assertExitCode(1);
});
