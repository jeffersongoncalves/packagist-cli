<?php

use App\Providers\AppServiceProvider;

return [
    'name' => 'Packagist',
    'version' => app('git.version'),
    'env' => 'development',
    'providers' => [
        AppServiceProvider::class,
    ],
];
