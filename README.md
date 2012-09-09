Gen
===

A *very* *very* simple static site generator using Twig.

I developed this *very* *very* simple static site generator specifically for generating the http://proemframework.org web site. It is indeed, *very* *very* simple.

It expects to find the following structure within the source directory:

```
├── assets
├── content
└── templates
```

The *assets* directory is optional, but if found, it's contents will be copied into the destination directory.

The *templates* directory should contain your reusable template partials, while, the *content* directory contains your site structure and actual pages.

The contents of the *content* directory will be processed and the directories and pages within will be copied into your destination.

eg;

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

will result in:

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

You can also inject variabes into your content by using php pages. Gen searches for a global.php file within the root of your source directory, a local.php file within the content directory or any of it's sub directories. Gen will finally search for a php file named the same as your twig file.Any of these php files can return an array of data which will then be made available within your templates.
