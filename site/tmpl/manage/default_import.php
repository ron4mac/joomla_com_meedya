<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

echo '<div id="impcbs">';
echo '<input type="checkbox" name="fast" id="fast" value="1"><label for="fast">'.Text::_('COM_MEEDYA_FILEASTITLE').'</label><br><br>';
echo '<span>'.Text::_('COM_MEEDYA_IMPSELECT').'</span><br>';
$folds = JFolder::folders($this->gallpath.'/import');
foreach ($folds as $k => $fold) {
	echo '<div class="impfld"><input type="checkbox" id="infld'.$k.'" name="impflds[]" value="'.$fold.'" class="impflds" onchange="watchFolders()">';
	echo '<label for="infld'.$k.'">'.$fold.'</label></div>';
}
echo '</div>';

?>
<script>
function $id (id) {
	return document.getElementById(id);
	//return jQuery('#'+id);
}
function watchFolders () {
	var actb = $id('imporb');
	var classes = actb.classList;
	if (document.querySelectorAll('input[name="impflds[]"]:checked').length) {
		classes.remove("btn-disabled");
		classes.add("btn-primary");
		actb.disabled = false;
	} else {
		classes.remove("btn-primary");
		classes.add("btn-disabled");
		actb.disabled = true;
	}
}
function createAlbum (elm) {
	elm.disabled = true;
	var albNamFld = $id('nualbnam');
	var albParFld = $id('h5u_palbum');
	var albDscFld = $id('albdesc');
	var nualbnam = albNamFld.value.trim();
//	elm.nextElementSibling.style.visibility = 'visible';
	var ajd = {task: 'manage.newAlbum', albnam: nualbnam, paralb: (albParFld ? albParFld.value : 0), albdesc: albDscFld.value};
	ajd[Meedya.formTokn] = 1;
	jQuery.post(Meedya.rawURL, ajd,
		function (response, status, xhr) {
//			console.log(response, status, xhr);
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
