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

`Obeyfile.php`:

```php
<?php
/*
 * The location of this file will be the root directory (rootDir) of the preprocessing
 * project.
 * 
 * Input files below this directory will be processed, and resulting output files will
 * be written to outputDir (see below) in the same relative locations to outputDir
 * as the locations of the input files relative to rootDir.
 */
return [
    
    /*
     * outputDir
     * Output directory into which generated files will be written. Can go upwards.
     * 
     * Default: "."
     */
    'outputDir' => '../../config/openresty',

    /*
     * inputs
     * Array of glob patterns describing the set of input files. These must be
     * file globs, and the directories are NOT searched recursively. You must add
     * an entry for each subdirectory that contains files you want to process.
     * 
     * Default: [ "*.php" ]
     */
    'inputs' => [
        'content-server/sites/*.php',
        'proxy/sites/proxy/locations/*.php',
        'proxy/sites/proxy/servers/*.php'
    ],

    /*
     * importPaths
     * Array of import search paths. When resolving import('foo/bar'), a candidate file path
     * will be generated from each <entry>:
     *  - <rootDir>/<entry>/foo/bar.php (if <entry> is a relative path path)
     *  - <entry>/foo/bar.php (if <entry> is an absolute path)
     * 
     * Default: [ 'include' ]
     */    
    'importPaths' => [
        'include',                  //relative to rootDir
        '/usr/share/obey/include'   //absolute
    ],
    
    /*
     * outputNameTemplate
     * Output filename template. The '{}' placeholder will be replaced by the part
     * matched by the glob star * in the input file pattern.
     * 
     * Default: '{}'
     */
    'outputNameTemplate' => '{}.nginx',
    
    /*
     * style
     * One of several option presets relating chiefly to output formatting.
     * 
     * Default: 'smart'
     * Useful values:
     * 
     *      'smart'
     *          simple whitespace-managed formatter that indents blocks inside
     *          curly braces
     * 
     *      'openresty'
     *          like 'smart', but also indents on some Lua keywords; useful for nginx
     *          configs with lua-nginx-module
     * 
     *      'dockerfile'
     *          outdents each text block (but not each line separately) as much as possible  
     */
    'style' => 'openresty'
];
```
