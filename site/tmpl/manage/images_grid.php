<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2024 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.3.4
*/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

echo '<div id="imggrid">
';

foreach ($this->iids as $item) {
	echo HtmlMeedya::imageThumbElement($item);
}

echo '</div>
';
