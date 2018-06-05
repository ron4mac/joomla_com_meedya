var redirURL = '',
	h5u_albSel = null;

/* a couple of utility functions to avoid using jquery and assist in minification */
// getElementById
function $id(id) {
	return document.getElementById(id);
}
// addEventListener
function $ae(elem, evnt, func) {
	elem.addEventListener(evnt, func, false);
}

/* action to be taken when all files are uploaded */
function H5up_done (errcnt) {
	var albact = '.php?album=' + h5u_albSel.value;
	if (js_vars.user_id > 0 || js_vars.guest_edit == 1) {
		redirURL = js_vars.site_url + '&task=manage.imgEdit&after=' + js_vars.timestamp;
	} else {
		redirURL = js_vars.site_url + '/thumbnails' + albact;
	}
	if ((js_vars.autoedit=='1') && (errcnt===0)) {
		window.location = redirURL;
		return;
	}
	$id('gotoedit').style.display = 'table-row';
}

function watchAlbNam (elm) {
	var creab = $id('creab');
	if (elm.value.trim()) {
		creab.disabled = false;
	} else {
		creab.disabled = true;
	}
}

function createAlbum (elm) {
	elm.disabled = true;
	var albNamFld = $id('nualbnam');
	var albParFld = $id('h5u_palbum');
	var nualbnam = albNamFld.value.trim();
	elm.nextElementSibling.style.visibility = 'visible';
	var ajd = {
		task: 'manage.newAlbum',
		albnam: nualbnam,
		paralb: (albParFld ? albParFld.value : 0),
		[Joomla.getOptions('csrf.token', '')]: 1,
		'o': 1
		};
	jQuery(h5u_albSel).load(js_vars.upLink, ajd, 
		function (response, status, xhr) {
			console.log(response, status, xhr);
			elm.nextElementSibling.style.visibility = 'hidden';
			if (status=="success") {
				var crea = $id("crealbm");
				crea.style.display = "none";
				album_select($id("h5u_album"));
			} else {
				alert(xhr.statusText);
			}
			elm.disabled = false;
		}
	);
}

// explain allowed file types (extensions)
function showAllowedExts() {
	alert(js_vars.h5uM.extallow + js_vars.allowed_file_types.join(', '));
}

function shide_titlrow(elem) {
	var targ = $id('titlrow');
	elem.checked ? targ.style.display = 'none' : targ.style.display = 'table-row';
}

function album_select(elm) {
	var asel = elm.options[elm.selectedIndex].value;
	var crea = $id("crealbm");
	crea.style.display = asel==-1 ? "inline-block" : "none";
	if (asel==-1) {
		var nam = $id("nualbnam");
		nam.focus();
	}
	$id("dzupui").style.display = asel<1 ? "none" : "block";
}

jQuery(document).ready(function() {
	h5u_albSel = document.getElementsByName('h5u_album')[0];
});
