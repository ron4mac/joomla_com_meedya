<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2023 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

/*	codes
	g - gallery common
	a - album css
	m - meedya css
	f - fancybox 3 css
	F - fancybox 4 css
	M - manage css
	r - ratings css
	p = pell css
	t - tag-input
	s - slides css
*/
// gamfFMrpts


function addStyle ($css)
{
	global $dbg, $cssfiles;

	if (!$dbg && file_exists($css.'.min.css')) {
		$cssfiles[$css.'.min.css'] = 1;
		return;
	}
	if (file_exists($css.'.css')) {
		$cssfiles[$css.'.css'] = 1;
	}
}

// D = debug (use un-minified files)
// DcmMfFrbaAuUptes

$c2css = [
	'g' => 'css/gallery',
	'a' => 'css/album',
	'm' => 'css/meedya',
	'M' => 'css/manage',
	'f' => 'vendor/fancybox/3.5.7/jquery.fancybox',
	'F' => 'vendor/fancybox/4.0.27/fancybox',
	'r' => 'css/rating',
	'U' => 'css/uplodr',
	'p' => 'vendor/pell/pell.min',
	't' => 'vendor/tags/jquery.tagsinput',
	's' => 'css/slides'
];

$dbg = false;
$cssfiles = [];
$codes = str_split($_GET['c']);

foreach ($codes as $c) {
	if ($c == 'D') {
		$dbg = true;
	} else {
		addStyle($c2css[$c]);
	}

/*	switch ($c) {
		case 'g':
			addStyle('css/gallery');
			break;
		case 'a':
			addStyle('css/album');
			break;
		case 'm':
			addStyle('css/meedya');
			break;
		case 'f':
			addStyle('vendor/fancybox/3.5.7/jquery.fancybox');
			break;
		case 'F':
			addStyle('vendor/fancybox/4.0.27/fancybox');
			break;
		case 'M':
			addStyle('css/manage');
			break;
		case 'r':
			addStyle('css/rating');
			break;
		case 'p':
			addStyle('vendor/pell/pell.min');
			break;
		case 't':
			addStyle('vendor/tags/jquery.tagsinput');
			break;
		case 's':
			addStyle('css/slides');
			break;
	}*/
}

$lastmod = 0;
$totsize = 0;
$csss = [];
foreach ($cssfiles as $cssf => $xv) {
	if (is_array($cssf)) {
		$totsize += strlen($cssf['s']) + 1;
		$csss[] = $cssf['s'];
	} else {
		$lastmod = max($lastmod, @filemtime($cssf));
		$fsz = @filesize($cssf);
		$totsize += ($fsz ?: 12) + strlen($cssf) + 6;
		$csss[] = $cssf;
	}
}
$hash = $lastmod . '-' . $totsize . '-' . md5(implode(':',$csss));

if (stripslashes($_SERVER['HTTP_IF_NONE_MATCH'] ?? '') == $hash)
{
	// Return visit and no modifications, so do not send anything 
	header ($_SERVER['SERVER_PROTOCOL'].' 304 Not Modified'); 
//	header ('Content-Length: 0'); 
} else {
	//package the script files for one access
	header('Access-Control-Expose-Headers: ETag');
	header('Content-type: text/css');
	header('Content-Length: ' . $totsize);
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastmod) . ' GMT');
	header('ETag: ' . $hash);
	header('Cache-Control: must-revalidate');
	foreach ($cssfiles as $cssf => $xv) {
		if (is_array($cssf)) {
			echo $cssf['s'];
		} else {
			echo"/*{$cssf}*/\n";
			if (!@readfile($cssf)) echo"/*MISSING*/\n";
		}
		echo"\n";
	}
}
