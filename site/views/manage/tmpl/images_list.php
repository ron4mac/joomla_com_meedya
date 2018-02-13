<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2017 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

echo '<div id="imglist">
';

foreach ($this->iids as $item) {
	//echo'<xmp>';var_dump($item);echo'</xmp>';
	echo '<div class="lstItem">';
	echo JHtml::_('meedya.imageThumbElement', $item);
//	echo "\n".'<img src="components/com_meedya/static/img/img.png" data-echo="thm/'.$item->file
//		.'" data-img="'.$item->file
//		.'" class="litem" onclick="return lboxImg(event, this)" />';
	echo '<div>'.$item->title.'</div>';
	echo '<div>'.$item->desc.'</div>';
	echo '<div>'.$item->album.'</div>';
	echo '<div>'.dateF($item->timed).'</div>';
	echo '<div>'.dateF($item->expodt).'</div>';
	echo '</div>';
}

echo '</div>
';
