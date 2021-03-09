<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2021 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

echo '<div id="imggrid">
';

foreach ($this->iids as $item) {
	echo JHtml::_('meedya.imageThumbElement', $item);
}

echo '</div>
';
