Usage
-----
  "scripts": {
    "post-install-cmd": [
      "Contributte\\Neonizer\\NeonizerExtension::process"
    ],
    "post-update-cmd": [
      "Contributte\\Neonizer\\NeonizerExtension::process"
    ]
  },
  "extra": {
    "neonizer": {
      "files": [
        {
          "dist-file": "files/config.neon.dist"
        },
        {
          "dist-file": "files/config.neon.dist",
          "file": "files/config.json"
        }
      ]
    }
  }