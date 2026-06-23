<?php

use App\Services\RepositoryResolver;

beforeEach(function () {
    $this->resolver = new RepositoryResolver;
});

it('keeps full https urls untouched', function () {
    expect($this->resolver->normalizeUrl('https://github.com/owner/repo'))
        ->toBe('https://github.com/owner/repo');
});

it('strips trailing slash from urls', function () {
    expect($this->resolver->normalizeUrl('https://github.com/owner/repo/'))
        ->toBe('https://github.com/owner/repo');
});

it('keeps ssh urls untouched', function () {
    expect($this->resolver->normalizeUrl('git@github.com:owner/repo.git'))
        ->toBe('git@github.com:owner/repo.git');
});

it('expands owner/repo shorthand to a github url', function () {
    expect($this->resolver->normalizeUrl('jeffersongoncalves/packagist-cli'))
        ->toBe('https://github.com/jeffersongoncalves/packagist-cli');
});

it('detects package names', function () {
    expect($this->resolver->isPackageName('jeffersongoncalves/packagist-cli'))->toBeTrue()
        ->and($this->resolver->isPackageName('https://github.com/owner/repo'))->toBeFalse()
        ->and($this->resolver->isPackageName('git@github.com:owner/repo.git'))->toBeFalse();
});
