# Simpliwiki

<http://github.com/benward/simpliwiki/>

Copyright (c) 2010 Ben Ward <http://benward.me>

MIT License.

Indebted to the work by Steven Frank on W2 Wiki. See footer for full acknowledgements, history and included libraries.

## Simpliwiki Design Goals

The reason for forking W2 was because these design goals differ from W2. W2 is a great, bare, flat structure note-taking wiki. The wiki I need is larger; reintroducing common wiki functionality like versioning, database storage, and (plausibly) more complex access control.

However, I require that functionality in a simple way. Existing, stable wiki software like MediaWiki, even PmWiki is big, complex. The solution is adopting W2 as a barebones but beautiful simple based (thank you, Steven Frank), and build from there.

My Simpliwiki goals, adapted from W2, are as follows:

1. Remain compact and simple. Resist over-engineering; do the minimum.
2. Short page generation execution times.
3. Simple, easy-to-read code.
4. Baked in Markdown, plus `[[wikipage]]` syntax linking.
5. Simple templates.
6. Support reversible base60 short URLs for content.
7. Keep database storage simple.
8. Generate perfect HTML5 output.
9. Maintain baseline support for W2 users.

## Installation

1. Create a directory for W2 somewhere in your web server's document
   root path.  It doesn't matter where.  W2 requires PHP 4 or higher.

2. Upload the W2 files to this directory.  When you're done, it should
   look something like this:

    w2/
        config.php
        images/
        index.css
        index.php
        markdown.php
        pages/
            Home.txt
            Markdown Syntax.txt
        README.txt

3. Make sure that the "images" and "pages" directories are writable by your
   web server process.

4. You may or may not need to edit config.php.  When you're ready, look in
   there for many additional configuration options.

You should now be ready to access your W2 installation.

## Basic Usage

While viewing a page:

* `[Edit]` edits the current page content.
* `[New]` creates a brand new page for you to edit.
* `[Upload]` allows you to upload an image to the images/ directory for later
  use with the {{image}} tag.  (see "Editing Syntax" below for more info)
* `[All]` shows you a list of all pages.
* `[Recent]` shows you a list of pages, most recently edited first.
* `[Home]` returns you to the Home page.

The search box performs a very simple brute-force search of the contents of all pages.

## Editing Syntax

Please refer to the [Markdown Syntax](http://daringfireball.net/projects/markdown/syntax)

Two additional syntax features have been added:

* `[[Page Name]]`: Double square brackets will insert a link to the named page.
* `{{ImageName.jpg}}`: Double curly brackets will insert the named image from the images/ directory.

## Optional Configuration

The file `config.php` contains many options for you to customize your W2 setup.

A few examples:

The following line in `config.php` may be changed if you do not want the
default page to be named 'Home':

    define('DEFAULT_PAGE', 'Home');

The following line in `config.php` may be changed if you'd like to use a
different CSS stylesheet:

    define('CSS_FILE', 'index.css');

The size of the edit textarea is controlled by:

    define('EDIT_ROWS', 18);

W2 has the ability to prompt for a password before allowing access to the
site.  Two lines in config.php control this:

    define('REQUIRE_PASSWORD', false);
    define('W2_PASSWORD', 'secret');

Set `REQUIRE_PASSWORD` to true and set `W2_PASSWORD` to the password you'd like to use. Or, better, store a sha1 hash of your password by setting `W2_PASSWORD_HASH`. See the comments in the


## License

You may re-use the source code to W2 for any purpose, as long as you retain
the copyright information from the beginning of this file.  Markdown and PHP
Markdown have their own license terms, which must also be observed.  You may
not use the names "Simpliwiki", "Ben Ward", "W2" or "Steven Frank" to endorse or promote your product without written permission from the relevant party.

Absolutely no warranty is provided.


## Reporting Bugs

Please file issues on Github <http://github.com/benward/simpliwiki>

## Acknowledgements

### W2 Wiki

<http://code.google.com/p/w2wiki/>

Copyright (c) 2007-2009 Steven Frank <http://stevenf.com/> and contributors

Contributors: Craig Hockenberry, Dan Sandler, pcdinh, Victor Rehorst, Abhi Beckert, and others! Thank you!

### PHP Markdown

<http://www.michelf.com/projects/php-markdown/>

Copyright (c) 2004-2007 Michel Fortin

### Original Markdown

<http://daringfireball.net/projects/markdown/>

Copyright (c) 2004-2006 John Gruber

## Inherited W2 History

1.1 (May 2008)

  - Support for web servers that run PHP as a CGI, rather than a module
  - Various other improvements

1.0.2 (September 5, 2007)

  - 'All Pages' can now be sorted by title or last modified date (vrehorst)
  - Added list of valid upload MIME types and file extensions (abhibeckert)
  - Added config settings to change textarea size (vrehorst)
  - Added page timestamp display support (vrehorst)
  - Added simple authentication mechanism (stevenf, vrehorst)

1.0.1 (July 16, 2007)

  - Added style selectors to toolbar links for easier styling
  - Added support for "cruftless URLs" (index.php/MyPage instead
    of index.php?page=MyPage)
  - Separated configuration options into their own file for
    easier updating of the main code
  - Added implementation of file_put_contents() for PHP 4
  - Added cache-overriding and improved viewport headers for
    better iPhone support
  - Added configuration variable for selecting a different CSS file
  - Streamlined sanitize() function and renamed to sanitizeFilename()
    to better indicate its purpose
  - Upload link now hides if uploading is disabled
  - Possibly other small things I forgot about!

1.0 (July 13, 2007)

  - Initial release!

