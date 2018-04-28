<?php
defined('_JEXEC') or die;

//JHtml::_('jquery.framework');

$folds = JFolder::folders($this->gallpath.'/import');
foreach ($folds as $k => $fold) {
	echo '<div class="impfld"><input type="checkbox" id="infld'.$k.'" name="impflds[]" value="'.$fold.'" class="impflds" onchange="watchFolders()" />';
	echo '<label for="infld'.$k.'">'.$fold.'</label></div>';
}

$ajaxlink = JUri::base().'index.php?option=com_meedya&format=raw';
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