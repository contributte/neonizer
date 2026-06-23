![](https://heatbadger.now.sh/github/readme/contributte/neonizer/)

<p align=center>
  <a href="https://github.com/contributte/neonizer/actions"><img src="https://badgen.net/github/checks/contributte/neonizer/master?cache=300"></a>
  <a href="https://coveralls.io/r/contributte/neonizer"><img src="https://badgen.net/coveralls/c/github/contributte/neonizer?cache=300"></a>
  <a href="https://packagist.org/packages/contributte/neonizer"><img src="https://badgen.net/packagist/dm/contributte/neonizer"></a>
  <a href="https://packagist.org/packages/contributte/neonizer"><img src="https://badgen.net/packagist/v/contributte/neonizer"></a>
</p>
<p align=center>
  <a href="https://packagist.org/packages/contributte/neonizer"><img src="https://badgen.net/packagist/php/contributte/neonizer"></a>
  <a href="https://github.com/contributte/neonizer"><img src="https://badgen.net/github/license/contributte/neonizer"></a>
  <a href="https://bit.ly/ctteg"><img src="https://badgen.net/badge/support/gitter/cyan"></a>
  <a href="https://bit.ly/cttfo"><img src="https://badgen.net/badge/support/forum/yellow"></a>
  <a href="https://contributte.org/partners.html"><img src="https://badgen.net/badge/sponsor/donations/F96854"></a>
</p>

<p align=center>
Website 🚀 <a href="https://contributte.org">contributte.org</a> | Contact 👨🏻‍💻 <a href="https://f3l1x.io">f3l1x.io</a> | Twitter 🐦 <a href="https://twitter.com/contributte">@contributte</a>
</p>

Neonizer processes NEON configuration templates and fills missing local values interactively or programmatically.

## Versions

| State       | Version | Branch   | Nette | PHP     |
|-------------|---------|----------|-------|---------|
| dev         | `^0.8`  | `master` | 3.3+  | `>=8.2` |
| stable      | `^0.7`  | `master` | 3.3+  | `>=8.2` |

## Installation

To install latest version of `contributte/neonizer` use [Composer](https://getcomposer.org).

```bash
composer require contributte/neonizer
```

## Processing

<p align=center>
  <img src=".docs/assets/neonizer.gif" alt="Neonizer">
</p>

Neonizer allows loading of a dist/template file with default parameters. Those can then be filled in a local config neon file using an interactive mode, as can be seen above in the gif.

Add `extra.neonizer` section to your composer.json.

```json
"extra": {
  "neonizer": {
    "files": [
      {
        "dist-file": "app/config/config.local.neon.dist"
      },
      {
        "dist-file": "app/config/config.local.neon.dist",
        "file": "app/config/config.server.neon"
      }
    ]
  }
}
```

You have to define:

- `dist-file` - Source (template/dist) file for parameters processing.

You optionally can define:

- `file` - Destination (result) file with processed parameters.
- By default, the resulting file is created by removing the trailing file extension (`.dist, .tpl, .template`).
- For example `app/config/config.local.neon.dist` results to `app/config/config.local.neon`.

Add post-install and post-update script to composer.json.

```json
"scripts": {
  "post-install-cmd": [
    "Contributte\\Neonizer\\NeonizerExtension::process"
  ],
  "post-update-cmd": [
    "Contributte\\Neonizer\\NeonizerExtension::process"
  ]
}
```

Try to run `composer install` or `composer update`.

## Validation

Neonizer is also able to validate the configuration non-interactively. Add the following script to `composer.json`.

```json
"extra": {
  "neonizer": {
    "files": [
      {
        "dist-file": "app/config/config.local.neon.dist",
        "file": "app/config/config.local.neon"
      }
    ]
  }
}
```

Also define composer script in `composer.json`.

```json
"scripts": {
  "validate-config": [
    "Contributte\\Neonizer\\NeonizerExtension::validate"
  ]
}
```

Then run `composer run validate-config`. The script will exit with a non-zero code if the destination file fails
to set any parameters required by dist-file. This can be run e.g. on production as a part of the deploy process to
abort the deploy if the configuration is not up-to-date.

## Set Variables

This feature is suitable for CI and deployment. You can easily set the configuration into NEON file programmatically.

Add special script into composer.json.

```json
"scripts": {
  "set-config": [
    "Contributte\\Neonizer\\NeonizerExtension::set"
  ]
}
```

Then run:

```bash
composer set-config -- $(pwd)/app/config/config.local.neon --database.host=localhost --database.user=neonizer
```

Do you like **environment variables**?

```bash
composer set-config -- $(pwd)/app/config/config.local.neon --database.host=$DATABASE_HOST --database.user=$DATABASE_USER
```

## Development

See [how to contribute](https://contributte.org) to this package. This package is currently maintained by these authors.

<a href="https://github.com/f3l1x">
    <img width="80" height="80" src="https://avatars2.githubusercontent.com/u/538058?v=3&s=80">
</a>

-----

Consider to [support](https://contributte.org/partners) **contributte** development team.
Also thank you for using this package.
