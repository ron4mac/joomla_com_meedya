/* a couple of utility functions to avoid using jquery and assist in minification */
// getElementById
function $id(id) {
	return document.getElementById(id);
}

function up_watchAlbNam (elm) {
	var creab = $id('creab');
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
		jQuery('#newalbdlg').modal();
		return;
	}
	var crea = $id("crealbm");
	crea.style.display = asel==-1 ? "inline-block" : "none";
	if (asel==-1) {
		var nam = $id("nualbnam");
		nam.focus();
	}
	$id("dzupui").style.display = asel<1 ? "none" : "block";
}

function createAlbum (elm) {
	elm.disabled = true;
	var albNamFld = $id('nualbnam');
	var albParFld = $id('h5u_palbum');
	var nualbnam = albNamFld.value.trim();
	var ajd = {
		task: 'manage.newAlbum',
		albnam: nualbnam,
		paralb: (albParFld ? albParFld.value : 0),
		[Joomla.getOptions('csrf.token', '')]: 1,
		'o': 1
		};
	jQuery("#h5u_album").load(js_vars.upLink, ajd,
		function (response, status, xhr) {
			console.log(response, status, xhr);
			if (status=="success") {
				jQuery('#newalbdlg').modal('hide');
				album_select($id("h5u_album"));
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