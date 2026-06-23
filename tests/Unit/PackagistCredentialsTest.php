<?php

use App\DTOs\PackagistCredentials;

it('builds from array', function () {
    $c = PackagistCredentials::fromArray(['username' => 'jeff', 'apiToken' => 'abc']);

    expect($c->username)->toBe('jeff')->and($c->apiToken)->toBe('abc');
});

it('serializes to array', function () {
    $c = new PackagistCredentials('jeff', 'abc');

    expect($c->toArray())->toBe(['username' => 'jeff', 'apiToken' => 'abc']);
});

it('validates presence of both fields', function () {
    expect((new PackagistCredentials('jeff', 'abc'))->isValid())->toBeTrue()
        ->and((new PackagistCredentials('', 'abc'))->isValid())->toBeFalse()
        ->and((new PackagistCredentials('jeff', ''))->isValid())->toBeFalse();
});
