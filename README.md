# lglass
Network looking glass utility in PHP


## Release statement

Version 0.1, here be dragons. I needed to publish the source code before sharing it to somebody.
Fancier install instructions will probably be given here later.

## How to use
Drop sources in appropriate web folder.

Duplicate `conf/config.sample.yaml` into `conf/config.yaml` and list your servers.

Be careful: the SSH library only works with `RSA` SSH keys in PEM format at this point.

To generate compatible keys, run the following:
```bash
ssh-keygen -m PEM -t rsa -b 3072 -f mykey

```

## PHP Module dependencies
  * cURL
  * SSH2
  * Yaml
