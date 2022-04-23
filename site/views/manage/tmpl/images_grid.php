<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

echo '<div id="imggrid">
';

foreach ($this->iids as $item) {
	echo HTMLHelper::_('meedya.imageThumbElement', $item);
}

echo '</div>
';
