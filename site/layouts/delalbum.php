<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2024 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.3.4
*/
defined('JPATH_BASE') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;

extract($displayData);	//itemId

$mmdl = HTMLHelper::_(
	'bootstrap.renderModal',
	'delact', // selector
	array( // options
		'title'  => Text::_('COM_MEEDYA_DELETE_ALBUM'),
		'footer' => HtmlMeedya::modalButtons('COM_MEEDYA_DELETE_ALBUM','Meedya.deleteAlbum(this)', 'deliB', false, 'btn btn-warning'),
		//'modalWidth' => 30
	),
	Text::_('COM_MEEDYA_DELETE_ALBUM_BLURB')
	.'<br /><br /><form name="dalbform" action="'.Route::_('index.php?option=com_meedya&view=manage&Itemid='.$itemId, false).'" method="POST">'
	.'<input type="checkbox" name="wipe" id="trashall" value="true" /><label for="trashall">'
	.Text::_('COM_MEEDYA_DELETE_ALL_IMAGES').'</label>'
	.'<input type="hidden" name="task" value="manage.delAlbum" />'
	.'<input type="hidden" name="'.Session::getFormToken().'" value="1" />'
	.'<input type="hidden" name="aid" value="" /></form>'
);
//remove the large modal css designation
echo str_replace(' modal-lg', '', $mmdl);
