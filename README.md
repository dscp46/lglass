# LGlass
Network looking glass utility in PHP

LGlass is a looking glass written in PHP that supports multiple brands of routers. 

It is written in such a way that you can easily extend support with your brand of network equipment.

To do so:
  * Copy `app/lib/drv_cisco.php` to `app/lib/drv_<manufacturer>.php`.
  * Edit the new file, then rename the class name to match the new file name.
  * Add the necessary command translations.

If you're not at ease with PHP, open an feature request ticket with the list of supported commands and their syntax.

This software inspires from [PHP Looking Glass v2.0](https://phplg.sourceforge.net/#phplg), written by Gabriella Paolini and Dashamir Hoxha.

## Supported equipments

| Brand / OS release | Support |
| --- | --- |
| Cisco IOS | Full support |
| Linux   | Partial support |
| Mikrotik (RouterOS 7) | Partial support |
| Ubiquiti Edgemax | Without VRFs |
| VyOS 1.4 | Full support |

## Release statement

Version 0.1, here be dragons. I needed to publish the source code before sharing it to somebody.

Fancier install and configuration instructions will probably be given here later.

## How to install
Drop sources in appropriate web folder.

Duplicate `conf/config.sample.yaml` into `conf/config.yaml` and list your servers.

Be careful: the SSH library only works with `RSA` SSH keys in PEM format at this point.

To generate compatible keys, run the following:
```bash
ssh-keygen -m PEM -t rsa -b 3072 -f mykey

```

Ensure your config directory is properly secured. 

## PHP Module dependencies
  * cURL
  * SSH2
  * Yaml
