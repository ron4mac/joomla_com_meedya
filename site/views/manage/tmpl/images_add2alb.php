<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2021 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$ajaxlink = JUri::base().'index.php?option=com_meedya&format=raw';
?>
<div id="crealbm">
	<?php if ($this->albums): ?>
		<div class="ad2albtop">
		<dl>
		<dt><label for="h5u_album">Select Album</label></dt>
		<dd>
			<select id="h5u_album" name="h5u_album" onchange="watchAlb(this)">
				<option value="-1">[ NEW ALBUM ]</option>
				<option value="0" selected="selected"><?=JText::_('COM_MEEDYA_H5U_SELECT')?></option>
				<?=JHtml::_('meedya.albumsHierOptions', $this->albums)?>
			</select>
		</dd>
		</dl>
		</div>
	<?php endif; ?>
	<div id="creanualb" style="display:none">
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
				<!-- <option value=""><?=JText::_('COM_MEEDYA_H5U_SELPAR')?></option> -->
				<option value="0"><?=JText::_('COM_MEEDYA_H5U_NONE')?></option>
				<?=JHtml::_('meedya.albumsHierOptions', $this->albums)?>
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
<script>
function $id (id) {
	return document.getElementById(id);
	//return jQuery('#'+id);
}
function watchAlb (elm) {
	var creab = $id('creab');
	var classes = creab.classList;
	if (elm.value > 0) {
		$id('creanualb').style.display = "none";
		classes.remove("btn-disabled");
		classes.add("btn-primary");
		creab.disabled = false;
	} else {
		classes.remove("btn-primary");
		classes.add("btn-disabled");
		creab.disabled = true;
		if (elm.value == -1) {
			$id('creanualb').style.display = "block";
		} else {
			$id('creanualb').style.display = "none";
		}
	}
}
function watchAlbNam (elm) {
	//var creab = $id('creab');	console.log(creab,elm.value);
	var creab = $id('creab');
	var classes = creab.classList;
	if (elm.value.trim()) {
		//creab.disabled = false;
		//creab.removeClass("btn-disabled").addClass("btn-primary");
		classes.remove("btn-disabled");
		classes.add("btn-primary");
		creab.disabled = false;
	} else {
		//creab.disabled = true;
		//creab.removeClass("btn-primary").addClass("btn-disabled");
		classes.remove("btn-primary");
		classes.add("btn-disabled");
		creab.disabled = true;
	}
}
function addItems2Album (elm) {
	elm.disabled = true;
	document.adminForm.albumid.value = $id('h5u_album').value;
	document.adminForm.nualbnam.value = $id('nualbnam').value;
	document.adminForm.nualbpar.value = $id('h5u_palbum').value;
	document.adminForm.nualbdesc.value = $id('albdesc').value;
	document.adminForm.task.value = 'manage.addItemsToAlbum';
	document.adminForm.submit();
}
function aj_addItems2Album (elm) {
	elm.disabled = true;
	var albNamFld = $id('nualbnam');
	var albParFld = $id('h5u_palbum');
	var albDscFld = $id('albdesc');
	var nualbnam = albNamFld.value.trim();
	var ajd = {format: 'raw', task: 'manage.addItemsToAlbum', albnam: nualbnam, paralb: (albParFld ? albParFld.value : 0), albdesc: albDscFld.value};
	ajd[formTokn] = 1;
	jQuery.post(myBaseURL, ajd,
		function (response, status, xhr) {
			console.log(response, status, xhr);
			if (status=="success") {
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
