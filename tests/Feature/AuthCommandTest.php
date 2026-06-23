<?php

beforeEach(function () {
    $this->tmp = sys_get_temp_dir().'/packagist-cli-feature-'.uniqid();
    putenv('XDG_CONFIG_HOME='.$this->tmp);
});

afterEach(function () {
    @unlink($this->tmp.'/packagist.json');
    @rmdir($this->tmp);
    putenv('XDG_CONFIG_HOME');
});

it('saves credentials non-interactively', function () {
    $this->artisan('auth --username=jeff --token=secret')
        ->assertExitCode(0);

    expect(is_file($this->tmp.'/packagist.json'))->toBeTrue();
});

it('warns when showing without stored credentials', function () {
    $this->artisan('auth --show')
        ->assertExitCode(1);
});

it('shows stored credentials with a masked token', function () {
    $this->artisan('auth --username=jeff --token=supersecret')->assertExitCode(0);

    $this->artisan('auth --show')
        ->expectsOutputToContain('jeff')
        ->assertExitCode(0);
});

it('forgets stored credentials', function () {
    $this->artisan('auth --username=jeff --token=secret')->assertExitCode(0);
    $this->artisan('auth --forget')->assertExitCode(0);

    expect(is_file($this->tmp.'/packagist.json'))->toBeFalse();
});
