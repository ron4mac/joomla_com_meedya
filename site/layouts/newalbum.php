<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2023 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
defined('JPATH_BASE') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

extract($displayData);	//albums,script

$body = '<div id="crealbm" class="mdymodal">
	<div class="nualbtop">
	<dl>
	<dt><label for="nualbnam">' . Text::_('COM_MEEDYA_ALBUM_NAME') . '</label></dt>
	<dd><input type="text" name="nualbnam" id="nualbnam" value="" onkeyup="Meedya.watchAlbNam(this)" /></dd>
	</dl>
	</div>';
if ($albums) $body .= '<div class="nualbtop">
	<dl>
	<dt><label for="h5u_palbum">' . Text::_('COM_MEEDYA_ALBUM_PARENT') . '</label></dt>
	<dd>
		<select class="form-select form-select-sm" id="h5u_palbum" name="h5u_palbum">
			<!-- <option value="">' . Text::_('COM_MEEDYA_H5U_SELPAR') . '</option> -->
			<option value="0">' . Text::_('COM_MEEDYA_H5U_NONE') . '</option>
			' . HTMLHelper::_('meedya.albumsHierOptions', $albums) . '
		</select>
	</dd>
	</dl>
	</div>';
$body .= '<dl style="clear:both;">
	<dt><label for="albdesc">' . Text::_('COM_MEEDYA_ALBUM_DESC') . '</label></dt>
	<dd><textarea id="albdesc" name="albdesc" class="nualbdsc form-control" rows="3"></textarea></dd>
	</dl>
	</div>';


$mmdl = HTMLHelper::_(
	'bootstrap.renderModal',
	'newalbdlg',
	['title' => Text::_('COM_MEEDYA_CREATE_NEW_ALBUM'),
	'footer' => HTMLHelper::_('meedya.modalButtons', 'COM_MEEDYA_H5U_CREALBM', $script, 'creab'),
	//'modalWidth' => '40'
	],
	$body
	);
//remove the large modal css designation
echo str_replace(' modal-lg', '', $mmdl);
