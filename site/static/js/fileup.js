/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
/* a couple of utility functions to avoid using jquery and assist in minification */
// getElementById
//function _id(id) {
//	return document.getElementById(id);
//}

function up_watchAlbNam (elm) {
	var creab = _id('creab');
	if (elm.value.trim()) {
		creab.disabled = false;
	} else {
		creab.disabled = true;
	}
}

function album_select (elm) {
	var asel = elm.options[elm.selectedIndex].value;
	if (asel==-1) {
		elm.value = '';
		jQuery('#newalbdlg').modal('show');
		return;
	}
	var crea = _id("crealbm");
	crea.style.display = asel==-1 ? "inline-block" : "none";
	if (asel==-1) {
		var nam = _id("nualbnam");
		nam.focus();
	}
	_id("dzupui").style.display = asel<1 ? "none" : "block";
}

function createAlbum (elm) {
	elm.disabled = true;
	var albNamFld = _id('nualbnam');
	var albParFld = _id('h5u_palbum');
	var nualbnam = albNamFld.value.trim();
	var ajd = {
		task: 'manage.newAlbum',
		albnam: nualbnam,
		paralb: (albParFld ? albParFld.value : 0),
		[Joomla.getOptions('csrf.token', '')]: 1,
		'o': 1
		};
	jQuery("#h5u_album").load(H5uOpts.upURL, ajd,
		function (response, status, xhr) {
			//console.log(response, status, xhr);
			if (status=="success") {
				jQuery('#newalbdlg').modal('hide');
				album_select(_id("h5u_album"));
			} else {
				alert(xhr.statusText);
			}
			elm.disabled = false;
		}
	);
}

function updStorBar (elem, val) {
	elem.style.width = val + "%";
	elem.innerHTML = val + "%";
	if (val > 90) {
		elem.style.backgroundColor = "#ff8888";
	} else if (val > 80) {
		elem.style.backgroundColor = "#fff888";
	}
}