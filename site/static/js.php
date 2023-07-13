<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2023 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

function addScript ($scr)
{
	global $dbg, $jsfiles;

	if (!$dbg && file_exists($scr.'.min.js')) {
		$jsfiles[] = $scr.'.min.js';
		return;
	}
	if (file_exists($scr.'.js')) {
		$jsfiles[] = $scr.'.js';
	}
}

// D = debug (use un-minified files)
// DcmMfFrbaAuUptes

$c2js = [
	'c' => 'js/common',
	'm' => 'js/meedya',
	'M' => 'js/manage',
	'f' => 'vendor/fancybox/3.5.7/jquery.fancybox',
	// NOTE: the v4 fancybox code wrapper has to be removed for it to work here
	'F' => 'vendor/fancybox/4.0.27/fancybox.umd',
	'r' => 'js/rating',
	'b' => 'js/my_bb',
	'a' => 'js/itm_dand',
	'A' => 'js/alb_dand',
	'u' => 'js/fileup',
	'U' => 'js/uplodr',
	'p' => 'vendor/pell/pell.min',
	't' => 'vendor/tags/jquery.tagsinput',
	'e' => 'js/echo',	//(lazy load)
	's' => 'js/slides'
];

$dbg = false;
$jsfiles = [];
$codes = str_split($_GET['c']);

foreach ($codes as $c) {
	if ($c == 'D') {
		$dbg = true;
	} else {
		addScript($c2js[$c]);
	}
}

$lastmod = 0;
$totsize = 0;
$jss = [];
foreach ($jsfiles as $jsf) {
	if (is_array($jsf)) {
		$totsize += strlen($jsf['s']) + 1;
		$jss[] = $jsf['s'];
	} else {
		$lastmod = max($lastmod, @filemtime($jsf));
		$fsz = @filesize($jsf);
		$totsize += ($fsz ?: 12) + strlen($jsf) + 6;
		$jss[] = $jsf;
	}
}
$hash = $lastmod . '-' . $totsize . '-' . md5(implode(':',$jss));

if (stripslashes($_SERVER['HTTP_IF_NONE_MATCH'] ?? '') == $hash) {
	// Return visit and no modifications, so do not send anything 
	header ($_SERVER['SERVER_PROTOCOL'].' 304 Not Modified');
} else {
	//package the script files for one access
	header('Access-Control-Expose-Headers: ETag');
	header('Content-type: text/javascript');
	header('Content-Length: ' . $totsize);
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastmod) . ' GMT');
	header('ETag: ' . $hash);
	header('Cache-Control: must-revalidate');
	foreach ($jsfiles as $jsf) {
		if (is_array($jsf)) {
			echo $jsf['s'];
		} else {
			echo"/*{$jsf}*/\n";
			if (!@readfile($jsf)) echo"/*MISSING*/\n";
		}
		echo"\n";
	}
}
