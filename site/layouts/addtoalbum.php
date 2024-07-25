<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2024 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.4.0
*/
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use RJCreations\Component\Meedya\Site\Helper\HtmlMeedya;

extract($displayData);	//albs,exca
?>
<div id="crealbm">
	<?php if ($albs): ?>
		<div class="ad2albtop">
		<dl>
		<dt><label for="h5u_album">Select Album</label></dt>
		<dd>
			<select class="form-select form-select-sm" id="h5u_album" name="h5u_album" onchange="Meedya.watchAlb(this)">
				<option value="-1">[ NEW ALBUM ]</option>
				<option value="0" selected="selected"><?=Text::_('COM_MEEDYA_H5U_SELECT')?></option>
				<?=HtmlMeedya::albumsHierOptions($albs, 0, $exca)?>
			</select>
		</dd>
		</dl>
		</div>
	<?php endif; ?>
	<div id="creanualb" style="display:none">
		<div class="nualbtop">
		<dl>
		<dt><label for="nualbnam">Album Name</label></dt>
		<dd><input type="text" name="nualbnam" id="nualbnam" value="" onkeyup="Meedya.watchAlbNam(this)" /></dd>
		</dl>
		</div>
	<?php if ($albs): ?>
		<div class="nualbtop">
		<dl>
		<dt><label for="h5u_palbum">Album Parent</label></dt>
		<dd>
			<select id="h5u_palbum" name="h5u_palbum">
				<!-- <option value=""><?=Text::_('COM_MEEDYA_H5U_SELPAR')?></option> -->
				<option value="0"><?=Text::_('COM_MEEDYA_H5U_NONE')?></option>
				<?=HtmlMeedya::albumsHierOptions($albs)?>
			</select>
		</dd>
		</dl>
		</div>
	<?php endif; ?>
		<dl style="clear:both;">
		<dt><label for="albdesc">Album Description</label></dt>
		<dd>
	<?php
	echo '<textarea id="albdesc" name="albdesc" class="nualbdsc form-control" rows="5"></textarea>';
	?>
		</dd>
		</dl>
	</div>
</div>
