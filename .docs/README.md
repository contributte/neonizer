# Contributte Neonizer

## Content

- [Setup](#setup)
- [Processing](#processing)
- [Validation](#validation)
- [Set variables](#set-variables)

## Setup

```bash
composer require contributte/neonizer
```

## Processing

![Neonizer](/.docs/assets/neonizer.gif?raw=true)

Neonizer allows loading of a dist/template file with default parameters. Those can then be filled in a local config neon file using an interactive mode, as can be seen above in the gif.

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
    - By default, the resulting file is created by removing the trailing file extension (`.dist, .tpl, .template`).
    - For example `app/config/config.local.neon.dist` results to `app/config/config.local.neon`.

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

## Set variables

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

Do you like **environment variables**?


```sh
composer set-config -- $(pwd)/app/config/config.local.neon --database.host=$DATABASE_HOST --database.user=$DATABASE_USER
```
