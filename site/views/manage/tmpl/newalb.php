<?php
defined('_JEXEC') or die;

//jimport( 'joomla.html.editor' );

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

JHtml::_('jquery.framework');
$jdoc = JFactory::getDocument();

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
	<dt>
	<button type="button" id="creab" class="btn btn-primary" onclick="createAlbum(this)" style="vertical-align:text-bottom"><?=JText::_('COM_MEEDYA_H5U_CREALBM')?></button>
	<img src="<?=JUri::base(true)?>/components/com_meedya/static/css/process.gif" style="vertical-align:baseline;visibility:hidden;" />
	</dt>
	<dd></dd>
	</dl>
</div>
<script>
function $id(id) {
	return document.getElementById(id);
}
function watchAlbNam(elm) {
	var creab = $id('creab');
	if (elm.value.trim()) {
		creab.disabled = false;
	} else {
		creab.disabled = true;
	}
}
function createAlbum(elm) {
	elm.disabled = true;
	var albNamFld = $id('nualbnam');
	var albParFld = $id('h5u_palbum');
	var albDscFld = $id('albdesc');
	var nualbnam = albNamFld.value.trim();
	elm.nextElementSibling.style.visibility = 'visible';
	var ajd = {task: 'manage.newAlbum', albnam: nualbnam, paralb: (albParFld ? albParFld.value : 0), albdesc: albDscFld.value};
	jQuery.post('index.php?option=com_meedya&format=raw', ajd, 
		function (response, status, xhr) {
			console.log(response, status, xhr);
			elm.nextElementSibling.style.visibility = 'hidden';
			if (status=="success") {
				var crea = $id("crealbm");
				crea.style.display = "none";
				window.location.reload(true);
			} else {
				alert(xhr.statusText);
			}
			elm.disabled = false;
		}
	);
}
</script>