<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2024 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.4.0
*/
defined('JPATH_BASE') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use RJCreations\Component\Meedya\Site\Helper\HtmlMeedya;

extract($displayData);	//albums,script

$body = '<div id="clnalbed" class="mdymodal">
	<div class="nualbtop">
	<dl>
	<dt><label for="clalbnam">' . Text::_('COM_MEEDYA_ALBUM_NAME') . '</label></dt>
	<dd><input type="text" name="clalbnamed" id="clalbnamed" value="" onkeyup="Meedya.watchAlbNam(this,\'clnabsv\')" /></dd>
	</dl>
	</div>';
if ($albums) $body .= '<div class="nualbtop">
	<dl>
	<dt><label for="cln_palbum">' . Text::_('COM_MEEDYA_ALBUM_PARENT') . '</label></dt>
	<dd>
		<select class="form-select form-select-sm" id="cln_palbumed" name="cln_palbumed">
			<!-- <option value="">' . Text::_('COM_MEEDYA_H5U_SELPAR') . '</option> -->
			<option value="0">' . Text::_('COM_MEEDYA_H5U_NONE') . '</option>
			' . HtmlMeedya::albumsHierOptions($albums) . '
		</select>
	</dd>
	</dl>
	</div>';
$body .= '<dl style="clear:both;">
	<dt><label for="clalbdesc">' . Text::_('COM_MEEDYA_ALBUM_DESC') . '</label></dt>
	<dd><textarea id="clalbdesced" name="clalbdesced" class="nualbdsc form-control" rows="3"></textarea></dd>
	</dl>
	<input type="hidden" id="clnaid" name="clnaid" value="">
	</div>';


$mmdl = HTMLHelper::_(
	'bootstrap.renderModal',
	'clnalbdlged',
	['title' => Text::_('COM_MEEDYA_CLONE_ALBEDT'),
	'footer' => HtmlMeedya::modalButtons('COM_MEEDYA_SAVE', $script, 'clnabsv', false),
	//'modalWidth' => '40'
	],
	$body
	);
//remove the large modal css designation
echo str_replace(' modal-lg', '', $mmdl);
