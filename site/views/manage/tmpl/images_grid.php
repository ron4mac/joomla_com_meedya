<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2019 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

echo '<div id="imggrid">
';

foreach ($this->iids as $item) {
	echo JHtml::_('meedya.imageThumbElement', $item);
/*	$iDat = 'data-iid="'.$item->id.'" data-echo="thm/'.$item->file.'" data-img="'.$item->file.'"';
	echo "\n"
	.'<div class="item">
	<img src="components/com_meedya/static/img/img.png" '.$iDat.' class="mitem" onclick="return slctImg(event, this)" />
	<div class="item-overlay top">
		<i class="icon-expand" onclick="lboxPimg(event, this)"></i>
		<i class="icon-info-2 pull-left"></i>
		<i class="icon-edit pull-right" onclick="editImg(event, this)"></i>
	</div>
</div>'; */
}

echo '</div>
';
