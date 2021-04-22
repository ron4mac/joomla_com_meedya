<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2021 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

?>
<div id="crealbm">
	<div class="nualbtop">
	<dl>
	<dt><label for="nualbnam">Album Name</label></dt>
	<dd><input type="text" name="nualbnam" id="nualbnam" value="" onkeyup="Meedya.watchAlbNam(this)" /></dd>
	</dl>
	</div>
<?php if ($this->albums): ?>
	<div class="nualbtop">
	<dl>
	<dt><label for="h5u_palbum">Album Parent</label></dt>
	<dd>
		<select id="h5u_palbum" name="h5u_palbum">
			<!-- <option value=""><?=Text::_('COM_MEEDYA_H5U_SELPAR')?></option> -->
			<option value="0"><?=Text::_('COM_MEEDYA_H5U_NONE')?></option>
			<?=HTMLHelper::_('meedya.albumsHierOptions', $this->albums)?>
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
