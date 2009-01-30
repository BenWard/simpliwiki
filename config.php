<?php

// w2wiki configuration file
// edit this to your liking.  We'll even describe what you should do!
// This is a PHP source file.  Pay heed to match quotes and parentheses. All PHP statements must end with a semicolon.
// Comments are any line that begins with two slashes.

// each setting is a DEFINE statement, in the form of define(settingname, value);
// don't change the settingname part, just the value part.
// String values should be enclosed in single quotes '' or double quotes ""
// Boolean values should be specified as true or false (without quotes around them)

// *** Site path settings ***
// The base system path to w2wiki.  You shouldn't have to change this.
// Value is a string, ostensibly a directory path...
define('BASE_PATH', getcwd());					// Omit any trailing slash

// The path to the raw text documents maintained by W2
define('PAGES_PATH', BASE_PATH . "/pages");					// Omit any trailing slash

// The base URI path to w2wiki.  You should change this if it doesn't work automatically!
// Value is a string, a well-formed URI to be precise.
define('BASE_URI', str_replace("/index.php", "", $_SERVER['SCRIPT_NAME']));	// Omit any trailing slash

// SELF is the path component of the URL to the main script,
// such as /w2/index.php
define('SELF', $_SERVER['SCRIPT_NAME']);

// VIEW is used when your server spawns PHP as a CGI instead of an
// internal module.  You can fix the ugly long URL with mod_rewrite.
//define('VIEW', '?action=view&page=');
define('VIEW', '');

// The name of the page to show as the "Home" page.
// Value is a string, the title of a page (case-sensitive!)
define('DEFAULT_PAGE', 'Home');

// The CSS file to load to style the wiki.
// You can take the default, modify it, and save it under a new name in this directory - then change the value below.
// Value is a string, the name and/or relative filesystem path to a CSS file.
define('CSS_FILE', 'index.css');

// *** File upload settings ***
// Whether or not to allow file uploading
// Value is a boolean, default false
define('DISABLE_UPLOADS', false);

// the file types we accept for file uploads.  This is a good idea for security.
// Value is a comma-separated string of MIME types.
define('VALID_UPLOAD_TYPES', 'image/jpeg,image/pjpeg,image/png,image/gif,application/pdf,application/zip,application/x-diskcopy');

// the filename extensions we accept for file uploads
// Value is a comma-separated string of filename extensions (case-sensitive!)
define('VALID_UPLOAD_EXTS', 'jpg,jpeg,png,gif,pdf,zip,dmg');

// *** Interface settings ***
// The format to use when displaying page modification times.
// See the manual for the PHP 'date()' function for the specification: http://php.net/manual/en/function.date.php
define('TITLE_DATE', 'j-M-Y g:i A');

// Define the size of the text area in terms of character rows and columns.
// Values are integers.
define('EDIT_ROWS', 18);

// *** Authentication settings ***
// Is a password required to access this wiki?
// Value is a boolean.
define('REQUIRE_PASSWORD', false);

// The password for the wiki.
// Replace 'secret' with your password to set your password.
// Value is a string.
define('W2_PASSWORD', 'secret');

// Alternate (more secure) password storage.
// To use a hashed password, Comment out the W2_PASSWORD definition above and uncomment
// this one, using the result of sha1('your_password') as the value.
// Value is a string.
// define('W2_PASSWORD_HASH', 'e5e9fa1ba31ecd1ae84f75caaa474f3a663f05f4');

// Allowed IPs array.  If non-empty, specifies IPs that are allowed access. 
// Ranges should work too, such as "32." for AT&T's entire subnet
// If empty, all IPs are allowed.
$allowedIPs = array();

// Autolink feature will automatically convert page titles into links
// to the named page. (experimental)
define(AUTOLINK_PAGE_TITLES, true);

?>
