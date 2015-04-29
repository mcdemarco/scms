# SCMS

SCMS is a simple, flat file CMS which serves Markdown files as HTML.  It uses a single PHP file plus a JavaScript file and an htaccess file to serve a directory tree of Markdown files.  SCMS has a configurable site menu, configurable theming using Bootswatch, automatic tables of contents for individual Markdown files (a la Wikipedia), pass-through of other file formats, and built-in 404 handling.

## Use

To run SCMS, you need the `.htaccess` file, `index.php`, the contents of the `js/` directory, and an Apache webserver with PHP and mod_rewrite set up.  [Here](http://ole.michelsen.dk/blog/setup-local-web-server-apache-php-osx-yosemite.html) are some simple instructions for setting them up on a Mac.

Once installed, you will be able to access files by name thus:

	content/index.md can be accessed at /.  
	content/text.md can be accessed at /text.  
	content/subdirectory/index.md can be accessed at /subdirectory/.  
	content/subdirectory/text.md can be accessed at /subdirectory/text.  

## Configuration

The content directory, file type, Bootstrap theme, site name and other settings are customized by editing the PHP variables at the top of `index.php`.  The default values and other options are explained below.

### Setup

#### $content_dir = 'content';

This is the directory containing your markdown files.  It can have as many levels of subdirectories as you need, but it should be located next to `index.php`.

You can symlink another directory rather than moving your content.  To do that from the command line, change directories into the directory where the `index.php` file is located.  If you have the sample content directory there, remove it.  Then type:

	ln -s /path/to/your/actual/directory content

If you have non-markdown content you want to serve normally from this directory, then you should either *not* rename it to something else, or you should edit the `.htaccess` file to match your new directory name.

#### $file_format = '.md';

Choose the extension on your markdown or text files. Be sure to include the period.  (An extension is required.)

#### $use_CDN = true;

If true, SCMS will load some JavaScript files from public CDNs.  If false, all files will be loaded from the js/lib directory, making it possible to run SCMS on your local webserver while offline.

Note that when you are actually offline, theme fonts will not be loaded and the default fonts will be displayed instead.

### Style

#### $site_name = 'SCMS';

The site name appears in the menus, so a shorter name is better.  You can also leave it blank or use an image.

#### $bootswatch_theme = 'spacelab';

Choose any Bootstrap theme from [Bootswatch](https://bootswatch.com) and it will be loaded from the CDN.  Only 'spacelab' is provided for offline use.

If you want to use a different Bootswatch theme offline, you should download it into a new directory under `bootswatch`: `js/lib/bootswatch/<theme_name>/bootstrap.min.css`.

#### $invert_nav = true;

Invert the navigation bar (menu) to the theme's alternate navbar color.

### Indexing

#### $index_filename = 'index';

SCMS will look for a file named this (plus the configured markdown file extension) when opening a directory, including the top-level directory.

#### $use_random = false;

When opening a directory that doesn't have a file with the default filename, the CMS can open a random Markdown file from the directory instead.

#### $menu_style = 'breadcrumbs';

The options are 'breadcrumbs', 'flat', 'filename', and 'none'.  The 'none' option removes all file-to-file navigation from the navigation bar; 'filename' is similar but displays the current filename in the navbar.

The 'breadcrumbs' option provides a breadcrumb trail from the top level of your content directory to your current file.  The current directory name is also a dropdown menu of the files in that directory.

The 'flat' option flattens the current directory into separate menu items, plus an 'up' menu item to go up one directory level.  This option is the most likely to go wrong if your directory or subdirectories contain too many files to fit into the menu.


## Credits

Copyright 2015 M.C.DeMarco, under the GNU Affero General Public License Version 3  
Inspired by Singularity CMS Copyright (c) 2012-2014 Christopher J. Su (inspired by Pico and Stacey).  
Uses [marked](https://github.com/chjj/marked/),
[Bootstrap without jQuery](https://github.com/tagawa/bootstrap-without-jquery), and
[Bootswatch](https://bootswatch.com).
