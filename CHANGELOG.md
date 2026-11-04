# Changelog

All notable changes to this project will be documented in this file.

## [1.2.0] - 2026-11-04

### Bug Fixes

- **deps:** Update guzzlehttp/guzzle to patch security advisories
- Pin PHP floor to 8.3 for Laravel Zero 13

### CI/CD

- Pin actions to commit SHA, add dependabot cooldown/composer, trim dist archive
- **release:** Generate CHANGELOG.md with git-cliff
- Pass release version input through env
- Standardize dependabot config
- Standardize tests workflow
- Remove run-tests.yml (replaced by tests.yml)
- Standardize tests workflow
- Auto-merge dependabot github-actions minor/patch

### Dependencies

- **deps:** Bump actions/cache from 5.1.0 to 6.1.0
- **deps:** Bump shivammathur/setup-php
- **deps:** Bump actions/checkout from 6.1.0 to 7.0.1
- **deps:** Bump orhun/git-cliff-action from 4.8.0 to 4.9.0
- **deps:** Bump orhun/git-cliff-action in the actions-deps group (#13)
- **deps:** Bump shivammathur/setup-php (#14)
- **deps:** Bump stefanzweifel/git-auto-commit-action (#16)

### Documentation

- Add Buy Me a Coffee sponsor link
- Standardize README section structure
- Point tests badge to tests.yml

### Miscellaneous Tasks

- Bump guzzlehttp/guzzle and guzzlehttp/psr7 for security advisories
- Add GitHub Sponsors to FUNDING.yml
- **deps-dev:** Bump the composer-deps group with 4 updates

### Other

- Auto-assign issues and pull requests

Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>

## [1.1.3] - 2026-06-29

### CI/CD

- Build PHAR before tagging so the tag embeds the released version

## [1.1.2] - 2026-06-29

### Bug Fixes

- Redact apiToken from leaked request URLs in error messages

## [1.1.1] - 2026-06-28

### Bug Fixes

- Serialize array payloads in PackagistApiException messages

## [1.1.0] - 2026-06-24

### Features

- Add show command to list all package versions

## [1.0.2] - 2026-06-23

### Features

- Adicionar comando self-update

## [1.0.1] - 2026-06-23

### Bug Fixes

- Clearer Packagist API error messages (prefer message, handle 404)
- Stop ignoring builds/ so the compiled binary is committed and shipped on Packagist

### CI/CD

- Add GitHub Actions workflows (tests, build, publish PHAR, pint, changelog), FUNDING, version.txt, CHANGELOG

### Documentation

- Add portfolio banner and README header

### Miscellaneous Tasks

- Bump version.txt to 1.0.1

## [1.0.0] - 2026-06-23

### Features

- Initial release of packagist-cli


