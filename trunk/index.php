<?php

/*
 * W2 1.0
 *
 * Copyright (C) 2007 Steven Frank <http://stevenf.com/>
 * Code may be re-used as long as the above copyright notice is retained.
 * See README.txt for full details.
 *
 * Written with Coda: <http://panic.com/coda/>
 */
 
include_once "markdown.php";

// User configurable options:

define(BASE_PATH, '/www/stevenf/w2');
define(DEFAULT_PAGE, 'Home');
define(DISABLE_UPLOADS, false);

function printToolbar()
{
	global $upage, $page;
	
	print "<div class=\"toolbar\">";
	print "<a href=\"index.php?action=edit&amp;page=$upage\">Edit</a> | ";
	print "<a href=\"index.php?action=new\">New</a> | ";
	print "<a href=\"index.php?action=upload\">Upload</a> | ";
	print "<a href=\"index.php?action=all\">All Pages</a> | ";
	print "<a href=\"index.php\">". DEFAULT_PAGE . "</a>";
	print "</div>\n";
}

function toHTML($inText)
{
	global $page;
	
	$inText = preg_replace("/\[\[(.*?)\]\]/", "<a href=\"index.php?page=\\1\">\\1</a>", $inText);
	$inText = preg_replace("/\{\{(.*?)\}\}/", "<img src=\"images/\\1\" alt=\"\\1\" />", $inText);
	$html .= Markdown($inText);
	
	return $html;
}

function sanitize($inText)
{
	$out = str_replace("..", "-", $inText);
	$out = str_replace("~", "-", $out);
	$out = str_replace("/", "-", $out);
	$out = str_replace("\\", "-", $out);
	$out = str_replace(":", "-", $out);
	
	return $out;
}

$action = $_REQUEST['action'];
$page = sanitize($_REQUEST['page']);
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
	$html = "<form method=\"post\" action=\"index.php\">\n";
	
	if ( $action == "edit" )
		$html .= "<input type=\"hidden\" name=\"page\" value=\"$page\" />";
	else
		$html .= "<p>Title: <input type=\"text\" name=\"page\" /></p>";

	if ( $action == "new" )
		$text = "";

	$html .= "<p><textarea name=\"newText\" rows=\"18\" cols=\"40\">$text</textarea></p>\n";
	$html .= "<p><input type=\"hidden\" name=\"action\" value=\"save\" />";
	$html .= "<input type=\"submit\" value=\"Save\" />\n";
	$html .= "<input type=\"button\" onclick=\"history.go(-1);\" value=\"Cancel\" /></p>\n";
	$html .= "</form>\n";
}
else if ( $action == "upload" )
{
	if ( DISABLE_UPLOADS )
	{
		$html .= "<p>Image uploading has been disabled on this installation.</p>";
	}
	else
	{
		$html .= "<form method=\"post\" action=\"index.php\" enctype=\"multipart/form-data\"><p>\n";
		$html .= "<input type=\"hidden\" name=\"action\" value=\"uploaded\" />";
		$html .= "<input type=\"file\" name=\"userfile\" />\n";
		$html .= "<input type=\"submit\" value=\"Upload\" />\n";
		$html .= "<input type=\"button\" onclick=\"history.go(-1);\" value=\"Cancel\" />\n";
		$html .= "</p></form>\n";
	}
}
else if ( $action == "uploaded" )
{
	if ( !DISABLE_UPLOADS )
	{
		$dstName = sanitize($_FILES['userfile']['name']);
		
		if ( move_uploaded_file($_FILES['userfile']['tmp_name'], 
				BASE_PATH . "/images/$dstName") === true ) 
		{
			$html = "<p class=\"note\">File '$dstName' uploaded</p>";
		}
		else
		{
			$html = "<p class=\"note\">Upload error</p>";
		}
	}
	
	$html .= toHTML($text);
}
else if ( $action == "save" )
{
	$newText = trim(stripslashes($_REQUEST['newText']));
	file_put_contents($filename, $newText);
	$html = "<p class=\"note\">Saved</p>";
	$html .= toHTML($newText);
}
else if ( $action == "all" )
{
	$html = "<ul>\n";

	$dir = opendir("pages");
	while ( $file = readdir($dir) )
	{
		if ( $file{0} == "." )
			continue;
			
		$file = preg_replace("/(.*?)\.txt/", "<a href=\"index.php?page=\\1\">\\1</a>", $file);
		$html .= "<li>$file</li>\n";
	}
	closedir($dir);

	$html .= "</ul>\n";
}
else if ( $action == "search" )
{
	$matches = 0;
	$q = $_REQUEST['q'];
	$html = "<h1>Search: $q</h1>\n<ul>\n";
	
	if ( trim($q) != "" )
	{
		$dir = opendir("pages");
		while ( $file = readdir($dir) )
		{
			if ( $file{0} == "." )
				continue;
				
			$text = file_get_contents("pages/$file");
			if ( eregi($q, $text) )
			{
				++$matches;			
				$file = preg_replace("/(.*?)\.txt/", "<a href=\"index.php?page=\\1\">\\1</a>", $file);
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

if ( $action == "all" )
	$title = "All Pages";
else if ( $action == "upload" )
	$title = "Upload Image";
else if ( $action == "new" )
	$title = "New";
else if ( $action == "search" )
	$title = "Search";
else
	$title = $page;

print "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
print "<html>\n";
print "<head>\n";
print "<meta name=\"viewport\" content=\"width=320\" />\n";
print "<link type=\"text/css\" rel=\"stylesheet\" href=\"index.css\" />\n";
print "<title>$title</title>\n";
print "</head>\n";
print "<body>\n";
print "<div class=\"titlebar\">$title</div>\n";
printToolbar();
print "<div class=\"main\">\n";
print "$html\n";
print "</div>\n";
printToolbar();
print "<form method=\"post\" action=\"index.php?action=search\">\n";
print "<div class=\"searchbar\">Search: <input type=\"text\" name=\"q\" /></div></form>\n";
print "</body>\n";
print "</html>\n";

?>
