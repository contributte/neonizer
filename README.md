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
    "neonizer": [
      {
        "file": "files/config.neon"
      }
    ]
}