Gen
=====
A *very* *very* simple static site generator using the Twig template engine.

I developed this *very* *very* simple static site generator specifically for generating the http://proemframework.org web site. It is indeed, *very* *very* simple.

Installation
============
The best way to install Gen is via [Composer](http://getcomposer.org/). The following, will create a composer.json file, install all dependencies and bootstrap a skeleton app.
```
composer init --require=trq/gen:0.0.1 --stability=dev -n && composer install && vendor/bin/gen -i
```

By default Gen expects to find the following structure within the source directory:

```
├── assets     - All contents are recursively copied into the destination directory. *(Optional)*
├── content    - .twig files processed, directories and .html pages produced copied into the destination.
├── extensions - Twig extensions. Any extension here is autoloaded as templates are processed. *(Optional)*
└── templates  - Contains reusable .twig template partials.
```
An Example
===
```
├── assets
│   ├── css
│   │   ├── bootstrap-responsive.css
│   ├── js
│   │   ├── bootstrap.min.js
│   └── media
│       └── img
│           ├── banner-bg.png
├── content
│   ├── about.twig
│   ├── docs
│   │   ├── current
│   │   │   ├── index.twig
│   └── index.twig
└── templates
    ├── layout.twig
```
__Results in__:

```
├── about.html
├── assets
│   ├── css
│   │   ├── bootstrap-responsive.css
│   ├── js
│   │   ├── bootstrap.min.js
│   └── media
│       └── img
│           ├── banner-bg.png
├── docs
│   ├── current
│   │   ├── index.html
└── index.html
```
Injecting Data
===
Data (variables, arrays, objects) can be injected into templates at certain levels.

Data is injected by simply creating a .php file that returns the data you want in an array.
```
<?php
  return ['somevar' => 'some value'];
```

Firstly, you can create a *global.php* file and drop it into the root of the *source* directory. This data will be available to all templates unless overwritten further down.

Secondly, you can create a *local.php* file within any sub directory. The data this file provides will be merged with the *global.php* file above (overwriting any duplicate keys).

Lastly, you can create a .php file with the same name as your .twig file. The data returned by this file will again be merged into the data provided above, and the results will be made available only within the .twig file of the same name.

Twig Extensions
===
To use a custom Twig extension simply create a class extending the [Gen\Twig\ExtensionBase](https://github.com/trq/Gen/blob/master/lib/Twig/ExtensionBase.php) within the *Gen* namespace and drop it's file into the *extensions* directory and extend the Gen\Twig\ExtensionBase class.

By extending the [Gen\Twig\ExtensionBase](https://github.com/trq/Gen/blob/master/lib/Twig/ExtensionBase.php) your extension will automatically gain access to the current directory being processed, the current file being processed, all options that have been passed into *Gen* and all *data* that is currently available.

For information about creating Twig Extensions see http://twig.sensiolabs.org/doc/advanced.html#creating-an-extension

Configuration
===
All directories and files used by *Gen* can be easily configured by placing a *gen.conf.php* file into the root of the *source* directory and having it return an array of the options you wish to overwrite. By default, this array looks like:
```
$ops = [
    'extensions'    => 'extensions',
    'content'       => 'content',
    'templates'     => 'templates',
    'assets'        => ['assets'],
    'global'        => 'global.php',
    'local'         => 'local.php',
    'src'           => $src,
    'dest'          => $dest
];
```
Where *$src* is either passed into *Gen* or uses the current working directory as default and *$dest* is either passed into *Gen* or uses *$src/build* by default.

Using the command line helper
===
*Gen* comes with a very simple command line utility used to process your site. It has available the following options:
```
-s <source>
-d <destination>
-v verbose output
-i init the base tree structure
-h help
```
All of these options are optional. If no *source* directory is provided the current working directory will be used.
