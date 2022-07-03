/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
/* globals Joomla */
'use strict';

(function(Meedya) {

	Meedya.album_select = (elm) => {
		//console.log(elm.value);
		let asel = elm.options[elm.selectedIndex].value;
		if (asel==-1) {
			elm.value = '';
			Meedya._oM('newalbdlg');
			Meedya._id('dzupui').style.display = 'none';
			return;
		}
		Meedya._id('dzupui').style.display = asel<1 ? 'none' : 'block';
	};

	Meedya.createAlbum = (elm) => {
		elm.disabled = true;
		let albNamFld = Meedya._id('nualbnam');
		let albParFld = Meedya._id('h5u_palbum');
		let albDscFld = Meedya._id('albdesc');
		let nualbnam = albNamFld.value.trim();
		let ajd = {
			albnam: nualbnam,
			paralb: (albParFld ? albParFld.value : 0),
			albdesc: albDscFld.value,
			[Joomla.getOptions('csrf.token', '')]: 1,
			'o': 1
			};
		Meedya._P('manage.newAlbum', ajd, (data) => {
			Meedya._cM('newalbdlg');
			let sela = Meedya._id('h5u_album');
			sela.innerHTML = data;
			Meedya.album_select(sela);
			elm.disabled = false;
		});
	};

	Meedya.updStorBar = (elm, val) => {
		elm.style.width = val + '%';
		elm.innerHTML = val + '%';
		if (val > 90) {
			elm.style.backgroundColor = '#ff8888';
		} else if (val > 80) {
			elm.style.backgroundColor = '#fff888';
		}
	};

	Meedya.itmUpldRslt = (rslt) => {
		let r = JSON.parse(rslt);
		if (r.qp) {
			Meedya.updStorBar(Meedya._id('qBar'), r.qp);
		}
		if (r.smsg) {
			console.warn(r.smsg);
		}
	};

})(window.Meedya = window.Meedya || {});