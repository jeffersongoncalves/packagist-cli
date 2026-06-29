# Changelog

All notable changes to `packagist-cli` will be documented in this file.

## 1.1.3 - 2026-06-29T14:41:35Z

**Full Changelog**: https://github.com/jeffersongoncalves/packagist-cli/compare/1.1.2...1.1.3

## 1.1.2 - 2026-06-29

### Security

- Redact the `apiToken` from error output. A cURL/connection failure used to surface Guzzle's raw exception message, which embeds the full request URL including the authentication token. The token is now masked (`apiToken=***`) before any error is shown. If you ran a failing `submit`/`update` on an earlier version, rotate your token at packagist.org → Profile → API Token.

## 1.1.1 - 2026-06-28

### Fixed

- **`submit` / API errors** — the Packagist API can return error bodies whose `message`, `error` or `details` fields are arrays (e.g. field validation errors). Building the exception message used to cast those arrays to a string, raising `Array to string conversion` and masking the real API error. Array payloads are now serialized to JSON so the actual Packagist error is shown.

## 1.1.0 - 2026-06-24

### What's new

- **`show` command** — list every published version/tag of a package returned by the Packagist API, with release date, PHP requirement and license. Supports `--limit` to cap the number of rows.

```bash
packagist show jeffersongoncalves/filament-ban
packagist show jeffersongoncalves/filament-ban --limit=10




```
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
