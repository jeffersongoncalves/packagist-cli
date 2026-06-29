<?php

use App\DTOs\PackagistCredentials;
use App\Exceptions\PackagistApiException;
use App\Services\AuthService;
use App\Services\PackagistClient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;

it('redacts the apiToken when a curl/connection error leaks the request URL', function () {
    $auth = Mockery::mock(AuthService::class);
    $auth->shouldReceive('load')->andReturn(new PackagistCredentials('jeff', 'secret-token-123'));

    // Simulate a cURL failure whose message embeds the full request URL (token in query).
    $request = new Request('POST', 'https://packagist.org/api/update-package?username=jeff&apiToken=secret-token-123');
    $handler = HandlerStack::create(new MockHandler([
        new ConnectException('cURL error 7: Failed to connect to '.(string) $request->getUri(), $request),
    ]));

    $client = new PackagistClient($auth, new Client(['handler' => $handler]));

    try {
        $client->updatePackage('https://github.com/jeff/pkg');
        $this->fail('Expected PackagistApiException');
    } catch (PackagistApiException $e) {
        expect($e->getMessage())
            ->not->toContain('secret-token-123')
            ->toContain('apiToken=***');
    }
});
