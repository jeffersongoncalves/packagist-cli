<?php

use App\Exceptions\PackagistApiException;

it('uses a string message verbatim', function () {
    $e = PackagistApiException::fromResponse(400, ['message' => 'Bad repo URL.']);

    expect($e->getMessage())->toBe('Bad repo URL.')
        ->and($e->statusCode)->toBe(400);
});

it('serializes an array message instead of casting it', function () {
    $body = ['status' => 'error', 'message' => ['repository' => ['The url is invalid.']]];

    $e = PackagistApiException::fromResponse(406, $body);

    expect($e->getMessage())->toBe('{"repository":["The url is invalid."]}')
        ->and($e->response)->toBe($body);
});

it('serializes an array error key', function () {
    $e = PackagistApiException::fromResponse(422, ['error' => ['a', 'b']]);

    expect($e->getMessage())->toBe('["a","b"]');
});

it('appends array details', function () {
    $e = PackagistApiException::fromResponse(400, [
        'message' => 'Failed.',
        'details' => ['field' => 'url'],
    ]);

    expect($e->getMessage())->toBe('Failed. {"field":"url"}');
});

it('falls back to a default message', function () {
    expect(PackagistApiException::fromResponse(404, [])->getMessage())->toBe('Package not found.')
        ->and(PackagistApiException::fromResponse(500, [])->getMessage())->toBe('Packagist API request failed.');
});
