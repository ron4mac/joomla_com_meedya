<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2024 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.3.4
*/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

echo '<style>
#imglist table, #imglist th, #imglist td {
	border: 1px solid #DDD;
	border-collapse: collapse;
}
#imglist th {
	border: 1px solid #AAA;
	background-color: aliceblue;
	padding: 0 1rem;
}
.lstItem, .item {
	margin:0;
}
.lstItem > div {
	margin:0;
}
.lstText, .lstDates, .lstSocial {
	padding: 1em;
	box-sizing: border-box;
}
.itemsTable td {
	vertical-align: top;
}
.itemsTable dl {
	margin: 0;
}
</style>';

echo '<div id="imglist">
<table class="itemsTable">
<tr class="thead"><th>Image</th><th>Title/Description</th><th>Dates</th><th>Social</th></tr>
';

foreach ($this->iids as $item) {
	echo '<tr>';
	//echo'<xmp>';var_dump($item);echo'</xmp>';
	echo '<td><div class="lstItem row-fluid">';
	echo HtmlMeedya::imageThumbElement($item);
	echo '</div></td>';
	echo '<td><div class="lstText row-fluid"><dl>';
	if ($item->title) echo '<dt>Title:</dt><dd>'.$item->title.'</dd>';
	if ($item->desc) echo '<dt>Description:</dt><dd>'.$item->desc.'</dd>';
	if ($item->kywrd) echo '<dt>Tags:</dt><dd>'.$item->kywrd.'</dd>';
	echo '</dl></div></td>';
	echo '<td><div class="lstDates row-fluid"><dl>';
	if ($item->expodt) echo '<dt>Exposure date:</dt><dd>'.dateF($item->expodt).'</dd>';
	echo '<dt>Upload date:</dt><dd>'.date('M j, Y, g:i a', $item->timeduts).'</dd>';
	echo '</dl></div></td>';
	echo '<td><div class="lstSocial row-fluid"><dl>';
	if ($item->cmntcnt) echo '<dt>Comments:</dt><dd>'.$item->cmntcnt.'</dd>';
	if ($item->ratetot) echo '<dt>Rating:</dt><dd>'.round($item->ratetot/$item->ratecnt,2).'</dd>';
	echo '</dl></div></td>';
	echo '</tr>';
}

echo '</table></div>
';
