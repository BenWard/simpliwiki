<?php

/*
 * W2 1.0.2
 *
 * Copyright (C) 2007 Steven Frank <http://stevenf.com/>
 * Code may be re-used as long as the above copyright notice is retained.
 * See README.txt for full details.
 *
 * Written with Coda: <http://panic.com/coda/>
 */
 
include_once "markdown.php";

// User configurable options:

include_once "config.php";

session_name("W2");
session_start();

if ( REQUIRE_PASSWORD && !isset($_SESSION['password']) )
{
	if (!defined('W2_PASSWORD_HASH'))
		define('W2_PASSWORD_HASH', sha1(W2_PASSWORD));
	
	if (( isset($_POST['p']) ) && ( sha1($_POST['p']) == W2_PASSWORD_HASH ))
		$_SESSION['password'] = W2_PASSWORD_HASH;
	else
	{
		print "<html><body><form method=\"post\"><input type=\"password\" name=\"p\"></form></body></html>";
		exit;
	}
}

// Support functions

function printToolbar()
{
	global $upage, $page,$action,$sortbar;

	print "<div class=\"toolbar\">";
//	if ($action == "edit")
//		print "<a class=\"tool first\" href=\"" . BASE_URI . "/index.php?action=rename&amp;page=$upage\">Rename</a> ";
//	else
		print "<a class=\"tool first\" href=\"" . BASE_URI . "/index.php?action=edit&amp;page=$upage\">Edit</a> ";
	print "<a class=\"tool\" href=\"" . BASE_URI . "/index.php?action=new\">New</a> ";

	if ( ! DISABLE_UPLOADS )
		print "<a class=\"tool\" href=\"" . BASE_URI . "/index.php?action=upload\">Upload</a> ";

 	print "<a class=\"tool\" href=\"" . BASE_URI . "/index.php?action=all_name\">All Pages</a> ";
 	print "<a class=\"tool\" href=\"" . BASE_URI . "/index.php\">". DEFAULT_PAGE . "</a>";
	if (REQUIRE_PASSWORD)
		print '<a class="tool" href="' . BASE_URI . '/index.php?action=logout">Logout</a>';
	print "</div>\n";
	if ($sortbar)
	{
		print '<div class="toolbar">';
		print '<a class="tool first" href="' . BASE_URI . '/index.php?action=all_name">Sort By Name</a>';
		print '<a class="tool" href="' . BASE_URI . '/index.php?action=all_date">Sort By Date</a>';
		print "</div>\n";
	}
}

function toHTML($inText)
{
	global $page;

 	$inText = preg_replace("/\[\[(.*?)\]\]/", "<a href=\"" . BASE_URI . "/index.php/\\1\">\\1</a>", $inText);
	$inText = preg_replace("/\{\{(.*?)\}\}/", "<img src=\"images/\\1\" alt=\"\\1\" />", $inText);
	$html = Markdown($inText);

	return $html;
}

function sanitizeFilename($inFileName)
{
	return str_replace(array('..', '~', '/', '\\', ':'), '-', $inFileName);
}

function destroy_session()
{
	if ( isset($_COOKIE[session_name()]) )
	{	error_log("COOKIE WAS SET");
		setcookie(session_name(), '', time()-42000, '/');
	}
	session_destroy();
	unset($_SESSION["password"]);
	unset($_SESSION);
}

// Support PHP4 by defining file_put_contents if it doesn't already exist

if ( !function_exists('file_put_contents') )
{
    function file_put_contents($n,$d)
    {
		$f = @fopen($n,"w");
		if ( !$f )
		{
			return false;
		}
		else
		{
			fwrite($f,$d);
			fclose($f);
			return true;
		}
    }
}

// Main code

if ( isset($_REQUEST['action']) )
	$action = $_REQUEST['action'];
else 
	$action = '';

if ( preg_match('@^/@', @$_SERVER["PATH_INFO"]) ) 
	$page = sanitizeFilename(substr($_SERVER["PATH_INFO"], 1));
else 
	$page = sanitizeFilename(@$_REQUEST['page']);

$upage = urlencode($page);

if ( $page == "" )
	$page = DEFAULT_PAGE;

$filename = BASE_PATH . "/pages/$page.txt";

if ( file_exists($filename) )
{
	$text = file_get_contents($filename);
}
else
{
	if ( $action != "save" )
		$action = "edit";
}

if ( $action == "edit" || $action == "new" )
{
	$formAction = BASE_URI . (($action == 'edit') ? "/index.php/$page" : "/index.php");
	$html = "<form id=\"edit\" method=\"post\" action=\"$formAction\">\n";

	if ( $action == "edit" )
		$html .= "<input type=\"hidden\" name=\"page\" value=\"$page\" />\n";
	else
		$html .= "<p>Title: <input id=\"title\" type=\"text\" name=\"page\" /></p>\n";

	if ( $action == "new" )
		$text = "";

	$html .= "<p><textarea id=\"text\" name=\"newText\" rows=\"" . EDIT_ROWS . "\" cols=\"" . EDIT_COLS . "\">$text</textarea></p>\n";
	$html .= "<p><input type=\"hidden\" name=\"action\" value=\"save\" />";
	$html .= "<input id=\"save\" type=\"submit\" value=\"Save\" />\n";
	$html .= "<input id=\"cancel\" type=\"button\" onclick=\"history.go(-1);\" value=\"Cancel\" /></p>\n";
	$html .= "</form>\n";
}
else if ( $action == "logout" )
{	error_log("DO LOGOUT");
	destroy_session();
	header("Location: " . BASE_URI . "/index.php");
	exit;
}
else if ( $action == "upload" )
{
	if ( DISABLE_UPLOADS )
	{
		$html = "<p>Image uploading has been disabled on this installation.</p>";
	}
	else
	{
		$html = "<form id=\"upload\" method=\"post\" action=\"" . BASE_URI . "/index.php\" enctype=\"multipart/form-data\"><p>\n";
		$html .= "<input type=\"hidden\" name=\"action\" value=\"uploaded\" />";
		$html .= "<input id=\"file\" type=\"file\" name=\"userfile\" />\n";
		$html .= "<input id=\"upload\" type=\"submit\" value=\"Upload\" />\n";
		$html .= "<input id=\"cancel\" type=\"button\" onclick=\"history.go(-1);\" value=\"Cancel\" />\n";
		$html .= "</p></form>\n";
	}
}
else if ( $action == "uploaded" )
{
	if ( !DISABLE_UPLOADS )
	{
		$dstName = sanitizeFilename($_FILES['userfile']['name']);
		$fileType = $_FILES['userfile']['type'];
		preg_match('/\.([^.]+)$/', $dstName, $matches);
		$fileExt = isset($matches[1]) ? $matches[1] : null;
		
		if (in_array($fileType, explode(',', VALID_UPLOAD_TYPES)) &&
			in_array($fileExt, explode(',', VALID_UPLOAD_EXTS)))
		{
			if ( move_uploaded_file($_FILES['userfile']['tmp_name'], 
				BASE_PATH . "/images/$dstName") === true ) 
			{
				$html = "<p class=\"note\">File '$dstName' uploaded</p>\n";
			}
			else
			{
				$html = "<p class=\"note\">Upload error</p>\n";
			}
		} else {
			$html = "<p class=\"note\">Upload error: invalid file type</p>\n";
		}
	}

	$html .= toHTML($text);
}
else if ( $action == "save" )
{
	$newText = trim(stripslashes($_REQUEST['newText']));
	file_put_contents($filename, $newText);
	
	$html = "<p class=\"note\">Saved</p>\n";
	$html .= toHTML($newText);
}
else if ( $action == "rename" )
{
	$html = "<form id=\"rename\" method=\"post\" action=\"" . BASE_URI . "/index.php\">";
	$html .= "<p>Title: <input id=\"title\" type=\"text\" name=\"page\" value=\"" . htmlspecialchars($page) . "\" />";
	$html .= "<input id=\"rename\" type=\"submit\" value=\"Rename\">";
	$html .= "<input id=\"cancel\" type=\"button\" onclick=\"history.go(-1);\" value=\"Cancel\" />\n";
	$html .= "<input type=\"hidden\" name=\"action\" value=\"renamed\" />";
	$html .= "<input type=\"hidden\" name=\"prevpage\" value=\"" . htmlspecialchars($page) . "\" />";
	$html .= "</p></form>";
}
else if ( $action == "renamed" )
{
	$pp = $_REQUEST['prevpage'];
	$pg = $_REQUEST['page'];
	error_log("RENAMING PAGE $pp -> $pg");
	$prevpage = sanitizeFilename($pp);
	$prevpage = urlencode($prevpage);
	
	$prevfilename = BASE_PATH . "/pages/$prevpage.txt";

	if (rename($prevfilename, $filename))
	{
		// Success.  Change links in all pages to point to new page
		if ($dh = opendir(BASE_PATH . "/pages/"))
		{
			while (($file = readdir($dh)) !== false)
			{
				$content = file_get_contents($file);
				$pattern = "/\[\[" . $pp . "\]\]/g";
				preg_replace($pattern, "[[$pg]]", $content);
				file_put_contents($file, $content);
			}
		}
	}
	else
	{
		
	}

}
else if ( $action == "all" )
{
	$html = "<ul>\n";
	$dir = opendir(BASE_PATH . "/pages");
	
	while ( $file = readdir($dir) )
	{
		if ( $file{0} == "." )
			continue;

		$file = preg_replace("/(.*?)\.txt/", "<a href=\"" . BASE_URI . "/index.php/\\1\">\\1</a>", $file);
		$html .= "<li>$file</li>\n";
	}

	closedir($dir);
	$html .= "</ul>\n";
	$sortbar = true;
}
else if ( $action == "all_name" )
{
	$html = "<ul>\n";
	$dir = opendir(BASE_PATH . "/pages");
	$filelist = array();
	while ( $file = readdir($dir) )
	{
		if ( $file{0} == "." )
			continue;

		$file = preg_replace("/(.*?)\.txt/", "<a href=\"" . BASE_URI . "/index.php/\\1\">\\1</a>", $file);
		array_push($filelist, $file);
	}

	closedir($dir);

	sort($filelist, SORT_LOCALE_STRING);
	for ($i = 0; $i < count($filelist); $i++)
	{
		$html .= "<li>" . $filelist[$i] . "</li>\n";
	}

	$html .= "</ul>\n";
	$sortbar = true;
}
else if ( $action == "all_date" )
{
	$html = "<ul>\n";
	$dir = opendir(BASE_PATH . "/pages");
	$filelist = array();
	while ( $file = readdir($dir) )
	{
		if ( $file{0} == "." )
			continue;
		$filelist[preg_replace("/(.*?)\.txt/", "<a href=\"" . BASE_URI . "/index.php/\\1\">\\1</a>", $file)] = filemtime(BASE_PATH . "/pages/$file");
	}

	closedir($dir);

	arsort($filelist, SORT_NUMERIC);
	foreach ($filelist as $key => $value)
	{
		$html .= "<li>$key (" .date(TITLE_DATE,$value) . ")</li>\n";
	}
	$html .= "</ul>\n";
	$sortbar = true;
}
else if ( $action == "search" )
{
	$matches = 0;
	$q = $_REQUEST['q'];
	$html = "<h1>Search: $q</h1>\n<ul>\n";

	if ( trim($q) != "" )
	{
		$dir = opendir(BASE_PATH . "/pages");
		
		while ( $file = readdir($dir) )
		{
			if ( $file{0} == "." )
				continue;

			$text = file_get_contents(BASE_PATH . "/pages/$file");
			
			if ( eregi($q, $text) )
			{
				++$matches;
				$file = preg_replace("/(.*?)\.txt/", "<a href=\"" . BASE_URI . "/index.php/\\1\">\\1</a>", $file);
				$html .= "<li>$file</li>\n";
			}
		}
		
		closedir($dir);
	}

	$html .= "</ul>\n";
	$html .= "<p>$matches matched</p>\n";
}
else
{
	$html = toHTML($text);
}

$datetime = '';

if (( $action == "all" ) || ( $action == "all_name") || ($action == "all_date"))
	$title = "All Pages";
else if ( $action == "upload" )
	$title = "Upload Image";
else if ( $action == "new" )
	$title = "New";
else if ( $action == "search" )
	$title = "Search";
else
{
	$title = $page;
	if (TITLE_DATE)
	{
		$datetime = "<span style=\"font-size: 10px\">(" . date(TITLE_DATE, @filemtime($filename)) . ")</span>";
	}
}
// Disable caching on the client (the iPhone is pretty agressive about this
// and it can cause problems with the editing function)

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

print "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
print "<html>\n";
print "<head>\n";

// Define a viewport that is 320px wide and starts with a scale of 1:1 and goes up to 2:1

print "<meta name=\"viewport\" content=\"width=320; initial-scale=1.0; maximum-scale=2.0;\" />\n";

print "<link type=\"text/css\" rel=\"stylesheet\" href=\"" . BASE_URI . "/" . CSS_FILE ."\" />\n";
print "<title>$title</title>\n";
print "</head>\n";
print "<body>\n";
print "<div class=\"titlebar\">$title $datetime</div>\n";

printToolbar();

print "<div class=\"main\">\n";
print "$html\n";
print "</div>\n";

printToolbar();

print "<form method=\"post\" action=\"" . BASE_URI . "/index.php?action=search\">\n";
print "<div class=\"searchbar\">Search: <input id=\"search\" type=\"text\" name=\"q\" /></div></form>\n";
print "</body>\n";
print "</html>\n";

?>
