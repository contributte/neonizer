# Neonizer

Interactive parameters (NEON/JSON) handling during composer install / update.

-----

[![Build Status](https://img.shields.io/travis/contributte/neonizer.svg?style=flat-square)](https://travis-ci.org/contributte/neonizer)
[![Code coverage](https://img.shields.io/coveralls/contributte/neonizer.svg?style=flat-square)](https://coveralls.io/r/contributte/neonizer)
[![Licence](https://img.shields.io/packagist/l/contributte/neonizer.svg?style=flat-square)](https://packagist.org/packages/contributte/neonizer)

[![Downloads this Month](https://img.shields.io/packagist/dm/contributte/neonizer.svg?style=flat-square)](https://packagist.org/packages/contributte/neonizer)
[![Downloads total](https://img.shields.io/packagist/dt/contributte/neonizer.svg?style=flat-square)](https://packagist.org/packages/contributte/neonizer)
[![Latest stable](https://img.shields.io/packagist/v/contributte/neonizer.svg?style=flat-square)](https://packagist.org/packages/contributte/neonizer)

## Discussion / Help

[![Join the chat](https://img.shields.io/gitter/room/contributte/contributte.svg?style=flat-square)](https://gitter.im/contributte/contributte)

## Versions

| State       | Version | Branch   | PHP      |
|-------------|---------|----------|----------|
| stable      | `^0.1`  | `master` | `>= 5.6` |

## Sample

![Neonizer](.docs/neonizer.gif)

## Install

Install via composer

```sh
composer require contributte/neonizer
```


## Usage

### Processing

Neonizer can load your dist/template file with default parameters and in interactive mode allow you fill config local. 
As you can see on gif above.

Add `extra.neonizer` section to your composer.json 

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
    - By default is result file guest removing trailing file extensions (`.dist, .tpl, .template`).
    - For example `app/config/config.local.neon.dist` dump `app/config/config.local.neon` file.

Add post-install and post-update script to composer.json

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

### Validation

Neonizer is also able to validate the configuration non-interactively. Add the following script to composer.json

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

Also define composer script in composer.json.

```json
"scripts": {
    "validate-config": [
      "Contributte\\Neonizer\\NeonizerExtension::validate"
    ]
}
```

Then run `composer run validate-config`. The script will exit with a non-zero code if the destination file forgets
to set any parameters required by dist-file. This can be run e.g. on production as a part of the deploy process to
abort the deploy if the configuration is not up-to-date.

### Set/Get

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

```sh
composer set-config -- $(pwd)/app/config/config.local.neon --database.host=localhost --database.user=neonizer
```

Do you like **environment parameters**? 


```sh
composer set-config -- $(pwd)/app/config/config.local.neon --database.host=$DATABASE_HOST --database.user=$DATABASE_USER
```

## Maintainers

<table>
  <tbody>
    <tr>
      <td align="center">
        <a href="https://github.com/f3l1x">
            <img width="150" height="150" src="https://avatars2.githubusercontent.com/u/538058?v=3&s=150">
        </a>
        </br>
        <a href="https://github.com/f3l1x">Milan Felix Šulc</a>
      </td>
      <td align="center">
        <a href="https://github.com/benijo">
            <img width="150" height="150" src="https://avatars3.githubusercontent.com/u/6731626?v=3&s=150">
        </a>
        </br>
        <a href="https://github.com/benijo">Josef Benjač</a>
      </td>
    </tr>
  <tbody>
</table>

-----

Thank you for testing, reporting and contributing.
