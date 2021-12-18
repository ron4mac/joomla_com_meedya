<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2021 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

echo HTMLHelper::_(
	'bootstrap.renderModal',
	'delact', // selector
	array( // options
		'title'  => 'Delete Album',
		'footer' => HTMLHelper::_('meedya.modalButtons', 'COM_MEEDYA_CREATE_DELETE_ALBUM','Meedya.deleteAlbum(this)', 'deliB', false, 'btn btn-warning'),
		'modalWidth' => 30
	),
	Text::_('COM_MEEDYA_CREATE_DELETE_ALBUM_BLURB')
	.'<br /><br /><input type="checkbox" name="trashall" id="trashall" value="true" /><label for="trashall">'
	.Text::_('COM_MEEDYA_CREATE_DELETE_ALL_IMAGES').'</label>'
);
