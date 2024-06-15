<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2024 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.3.4
*/
defined('JPATH_BASE') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

extract($displayData);	//albums,script

$body = '<div id="clnalbm" class="mdymodal">
	<div class="nualbtop">
	<dl>
	<dt><label for="clalbnam">' . Text::_('COM_MEEDYA_ALBUM_NAME') . '</label></dt>
	<dd><input type="text" name="clalbnam" id="clalbnam" value="" onkeyup="Meedya.watchAlbNam(this,\'clnab\')" /></dd>
	</dl>
	</div>';
if ($albums) $body .= '<div class="nualbtop">
	<dl>
	<dt><label for="cln_palbum">' . Text::_('COM_MEEDYA_ALBUM_PARENT') . '</label></dt>
	<dd>
		<select class="form-select form-select-sm" id="cln_palbum" name="cln_palbum">
			<!-- <option value="">' . Text::_('COM_MEEDYA_H5U_SELPAR') . '</option> -->
			<option value="0">' . Text::_('COM_MEEDYA_H5U_NONE') . '</option>
			' . HtmlMeedya::albumsHierOptions($albums) . '
		</select>
	</dd>
	</dl>
	</div>';
$body .= '<dl style="clear:both;">
	<dt><label for="clalbdesc">' . Text::_('COM_MEEDYA_ALBUM_DESC') . '</label></dt>
	<dd><textarea id="clalbdesc" name="clalbdesc" class="nualbdsc form-control" rows="3"></textarea></dd>
	</dl>
	<input type="hidden" id="oaid" name="oaid" value="'.$itemId.'">
	</div>';


$mmdl = HTMLHelper::_(
	'bootstrap.renderModal',
	'clnalbdlg',
	['title' => Text::_('COM_MEEDYA_CLONE_ALBUM'),
	'footer' => HtmlMeedya::modalButtons('COM_MEEDYA_CLNALBM', $script, 'clnab'),
	//'modalWidth' => '40'
	],
	$body
	);
//remove the large modal css designation
echo str_replace(' modal-lg', '', $mmdl);
