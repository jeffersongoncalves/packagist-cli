<div class="filament-hidden">

![packagist-cli](https://raw.githubusercontent.com/jeffersongoncalves/packagist-cli/main/art/jeffersongoncalves-packagist-cli.png)

</div>

# packagist-cli

A small CLI to publish and manage your Composer packages on [Packagist](https://packagist.org)
directly from the terminal — submit a new package, force a re-crawl, and inspect what you
have published. It stores your Packagist API token locally in `~/.config/packagist.json`
(`0600`) so you only authenticate once.

Built with [Laravel Zero](https://laravel-zero.com).

## Installation

```bash
composer global require jeffersongoncalves/packagist-cli
```

Or download the PHAR from the [releases page](https://github.com/jeffersongoncalves/packagist-cli/releases).

## Authentication

Grab your API token from <https://packagist.org/profile/> and store it:

```bash
packagist auth
# or non-interactively
packagist auth --username=you --token=xxxxxxxx
```

The token is written to `$XDG_CONFIG_HOME/packagist.json` (falling back to
`~/.config/packagist.json`) with `0600` permissions.

| Flag | Description |
|------|-------------|
| `--username=` | Packagist username (skips the prompt) |
| `--token=` | Packagist API token (skips the prompt) |
| `--show` | Print the stored credentials (token masked) |
| `--forget` | Remove the stored credentials |

## Commands

### `submit` — register a new package

```bash
packagist submit https://github.com/jeffersongoncalves/laravel-zero-support
# shorthand (assumed GitHub):
packagist submit jeffersongoncalves/laravel-zero-support
```

### `update` — force a re-crawl

```bash
packagist update jeffersongoncalves/laravel-zero-support
# or by repository URL
packagist update https://github.com/jeffersongoncalves/laravel-zero-support
```

### `list` — list a vendor's packages

```bash
packagist list jeffersongoncalves
# defaults to the authenticated username when omitted
packagist list
```

### `info` — inspect a package

```bash
packagist info jeffersongoncalves/laravel-zero-support
```

### `show` — list every published version

```bash
packagist show jeffersongoncalves/filament-ban
# limit the number of versions shown
packagist show jeffersongoncalves/filament-ban --limit=10
```

## Development

```bash
composer install
composer test        # pest + pint --test
composer build       # build the PHAR into builds/
```

## License

MIT © Jefferson Gonçalves
