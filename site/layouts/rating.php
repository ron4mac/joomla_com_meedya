<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
defined('JPATH_BASE') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

$smmdl = HTMLHelper::_(
	'bootstrap.renderModal',
	'rating-modal', // selector
	array( // options
		'title'  => Text::_('COM_MEEDYA_RATING_TITLE'),
		//'modalWidth' => 20
	),
	'<div class="rated"><span id="unrating" class="rating"></span></div>'
);

echo str_replace('modal-lg', 'modal-sm', $smmdl);