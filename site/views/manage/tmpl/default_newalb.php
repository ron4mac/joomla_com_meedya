<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2021 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

$ajaxlink = JUri::base().'index.php?option=com_meedya&format=raw';
?>
<div id="crealbm">
	<div class="nualbtop">
	<dl>
	<dt><label for="nualbnam">Album Name</label></dt>
	<dd><input type="text" name="nualbnam" id="nualbnam" value="" onkeyup="watchAlbNam(this)" /></dd>
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
<script>
function $id (id) {
	return document.getElementById(id);
	//return jQuery('#'+id);
}
function watchAlbNam (elm) {
	//var creab = $id('creab');	console.log(creab,elm.value);
	var creab = $id('creab');	console.log(creab,elm.value);
	var classes = creab.classList;
	if (elm.value.trim()) {
		classes.remove("btn-disabled");
		classes.add("btn-primary");
		creab.disabled = false;
	} else {
		classes.remove("btn-primary");
		classes.add("btn-disabled");
		creab.disabled = true;
	}
}
function ae_createAlbum (elm) {
	elm.disabled = true;
	var albNamFld = $id('nualbnam');
	var albParFld = $id('h5u_palbum');
	var albDscFld = $id('albdesc');
	var nualbnam = albNamFld.value.trim();
	var ajd = {task: 'manage.newAlbum', albnam: nualbnam, paralb: (albParFld ? albParFld.value : 0), albdesc: albDscFld.value};
	ajd[formTokn] = 1;
	jQuery.post(Meedya.rawURL, ajd,
		function (response, status, xhr) {
			console.log(response, status, xhr);
			if (status=="success") {
				jQuery('#newalbdlg').modal('hide');
				if (response) {
					alert(response);
				} else {
					window.location.reload(true);
				}
			} else {
				alert(xhr.statusText);
			}
			elm.disabled = false;
		}
	);
}
</script>
