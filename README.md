# obey
Minimalistic template processor that uses PHP directly as template language,
for generating text in configuration languages that miss some cool
features.

Created to overcome DRY noncompliance of the Dockerfile DSL (hence the name).

Developed further to help with injecting code into multiple
configuration files in order to configure one feature in nginx.

#### Installation
```
composer config --global repositories.obey vcs https://github.com/rulatir/obey
composer global require rulatir/obey:dev-master
```

#### Before using
```
export PATH=$HOME/.composer/vendor/bin:$PATH
```

#### Use
```
obey -f path/to/Obeyfile.php
```

(more to come)