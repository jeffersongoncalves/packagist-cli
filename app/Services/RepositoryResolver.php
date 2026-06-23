<?php

namespace App\Services;

class RepositoryResolver
{
    /**
     * Normalize user input into a full VCS repository URL.
     *
     * Accepts:
     *  - full URL: https://github.com/owner/repo(.git)
     *  - SSH URL:  git@github.com:owner/repo.git
     *  - shorthand: owner/repo  (assumed GitHub)
     */
    public function normalizeUrl(string $input): string
    {
        $input = trim($input);

        if (preg_match('#^(https?://|git@)#i', $input) === 1) {
            return rtrim($input, '/');
        }

        if (preg_match('#^[\w.-]+/[\w.-]+$#', $input) === 1) {
            return 'https://github.com/'.$input;
        }

        return $input;
    }

    /**
     * True when the input looks like a Packagist package name (vendor/name) and
     * not a URL.
     */
    public function isPackageName(string $input): bool
    {
        $input = trim($input);

        return preg_match('#^(https?://|git@)#i', $input) !== 1
            && preg_match('#^[\w.-]+/[\w.-]+$#', $input) === 1;
    }
}
