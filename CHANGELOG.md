# Changelog

All notable changes to `packagist-cli` will be documented in this file.

## 1.0.2 - 2026-06-23

Add the `self-update` command — update the packagist CLI to the latest release directly from the terminal.

## 1.0.1 - 2026-06-23

Ship the compiled `builds/packagist` binary in the package so `composer global require` installs a working `packagist` bin. Clearer Packagist API error messages.

## 1.0.0 - 2026-06-22

### Added

- `auth` — authenticate with Packagist and store the API token locally in `~/.config/packagist.json` (`0600`). Supports `--username`, `--token`, `--show`, `--forget`.
- `submit <repository>` — register a new package on Packagist from its repository URL or `owner/repo` shorthand.
- `update <package>` — force Packagist to re-crawl an existing package (by name or repository URL).
- `list [vendor]` — list packages published by a vendor (defaults to the authenticated username).
- `info <package>` — show details about a published package.
