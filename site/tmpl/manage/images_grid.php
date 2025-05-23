<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2024 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.4.0
*/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use RJCreations\Component\Meedya\Site\Helper\HtmlMeedya;

echo '<div id="imggrid">
';

foreach ($this->iids as $item) {
	echo HtmlMeedya::imageThumbElement($item);
}

echo '</div>
';
