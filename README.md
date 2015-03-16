# SCMS

SCMS is a simple, flat file CMS which serves Markdown files as HTML.  It is based on Singularity CMS, which was short and concise at 40 lines of PHP code and an htaccess file, until I forked it and added a few bits.  New features include automatic site menus, automatic tables of contents for individual Markdown files (a la Wikipedia), and automatic 404s (so you don't need to add a 404 page to your content directory).

## Customization

The content directory, file type, Bootstrap theme, site name and other settings can be easily customized by simply editing the PHP variables at the top of `index.php`:

	$content =  'content'; // 'content' is the folder containing your markdown files
	$site_name = 'scms'; // the site name appears in the menus, so shorter is better
	$bootswatch_theme = "spacelab"; // choose any bootstrap theme included in strapdown.js
	$invert_nav = true; // invert the bootstrap navbar
	$file_format = '.md'; // this is the extension on your markdown or text files (with the period).
	$index = 'index'; // the default file to open in each directory
	$use_random = false; // open a random file if a default file isn't found (TODO)
	$use_CDN = true; // change this to false to serve javascript files locally (for speed or offline use)


## URLs

A file at content/index.html can be accessed at /.  
A file at content/text.md can be accessed at /text.  
A file at content/sub/index.md can be accessed at /content/sub/.  
A file at content/sub/text.md can be accessed at /sub/text.  

## Credits

Copyright 2015 M.C.DeMarco, under the GNU Affero General Public License Version 3  
Inspired by Singularity CMS Copyright (c) 2012-2014 Christopher J. Su (inspired by Pico and Stacey.)  
Uses [marked](https://github.com/chjj/marked/),
[Bootstrap without jQuery](https://github.com/tagawa/bootstrap-without-jquery), and
[Bootswatch](https://bootswatch.com).
