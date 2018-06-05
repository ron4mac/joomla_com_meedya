<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2018 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

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
//$editor = JEditor::getInstance(JFactory::getConfig()->get('editor'));
//$params = array('smilies'=>0,'style'=>1,'layer'=>0,'table'=>0,'clear_entities'=>0,'mode'=>0);
//echo $editor->display('albdesc', '', '85%', 100, 80, 10, false, null, null, null, $params);
echo '<textarea id="albdesc" name="albdesc" class="nualbdsc form-control" rows="5"></textarea>';
?>
	</dd>
<!--	<dt>
	<button type="button" id="cccreab" class="btn btn-primary" onclick="createAlbum(this)" style="vertical-align:text-bottom"><?=JText::_('COM_MEEDYA_H5U_CREALBM')?></button>
	<img src="<?=JUri::base(true)?>/components/com_meedya/static/css/process.gif" style="vertical-align:baseline;visibility:hidden;" />
	</dt>
	<dd></dd>	-->
	</dl>
</div>
<script>
function $id (id) {
	return document.getElementById(id);
	//return jQuery('#'+id);
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
function createAlbum (elm) {
	elm.disabled = true;
	var albNamFld = $id('nualbnam');
	var albParFld = $id('h5u_palbum');
	var albDscFld = $id('albdesc');
	var nualbnam = albNamFld.value.trim();
//	elm.nextElementSibling.style.visibility = 'visible';
	var ajd = {format: 'raw', task: 'manage.newAlbum', albnam: nualbnam, paralb: (albParFld ? albParFld.value : 0), albdesc: albDscFld.value};
	ajd[formTokn] = 1;
	jQuery.post(myBaseURL, ajd, 
		function (response, status, xhr) {
			console.log(response, status, xhr);
//			elm.nextElementSibling.style.visibility = 'hidden';
			if (status=="success") {
				if (response) {
					alert(response);
//				var crea = $id("crealbm");
//				crea.style.display = "none";
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