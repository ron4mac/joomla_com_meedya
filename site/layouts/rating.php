<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2021 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

use Joomla\CMS\HTML\HTMLHelper;

echo HTMLHelper::_(
	'bootstrap.renderModal',
	'rating-modal', // selector
	array( // options
		'title'  => 'Test Title',
	//	'modalWidth' => 30
	),
	'<div class="rated"><span id="unrating" class="rating"></span></div>'
);
