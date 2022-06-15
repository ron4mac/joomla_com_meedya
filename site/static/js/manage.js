/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
/* globals Joomla,My_bb */
'use strict';

(function(Meedya, my, w) {

	// establish some common variables
	const formTokn = Joomla.getOptions('csrf.token');

	const _removeAlbThm = () => {
		Meedya._id('albthmimg').src = 'components/com_meedya/static/img/img.png';
		Meedya._id('albthmid').value = 0;
	};

	const _handleAlbthmDragOver = (e) => {
		if (e.dataTransfer.types.indexOf('imgsrc') < 0) return;
		Meedya._pd(e);
		return false;
	};

	const _handleAlbthmDrop = (e) => {
		Meedya._pd(e);
		let src = e.dataTransfer.getData('imgsrc');
		if (src) {
			let aimg = e.target.parentElement.getElementsByTagName("IMG")[0];
			aimg.src = src;
			aimg.style.opacity = null;
			let atv = Meedya._id('albthmid');
			atv.value = e.dataTransfer.getData('meeid');
		}
	};

	const _hasSelections = (sel, alrt=false) => {
		if (document.querySelectorAll(sel).length) {
			return true;
		} else {
			if (alrt) My_bb.alert(Meedya._T('COM_MEEDYA_SELECT_SOME'));
			return false;
		}
	};

	// shortcuts to common Meedya functions
	const openMdl = Meedya._oM;
	const closMdl = Meedya._cM;
	const postAction = Meedya._P;


//@@@@@@@@@@ PUBLIC FUNCTIONS @@@@@@@@@@

	// we'll export the postAction
	Meedya.postAction = postAction;

	Meedya.setAlbumDanD = () => {
		let albthm = Meedya._id("albthm");
		Meedya._ae(albthm, 'dragover', _handleAlbthmDragOver);
		Meedya._ae(albthm, 'drop', _handleAlbthmDrop);
		Meedya._ae(albthm, 'dragenter', (e) => { Meedya._pd(e); e.target.style.opacity = '0.5'; });
		Meedya._ae(albthm, 'dragleave', (e) => e.target.style.opacity = null);
		let albfrm = Meedya._id("albForm");
		Meedya._ae(albfrm, 'dragstart', (e) => e.dataTransfer.setData('albthm','X'));
		Meedya._ae(albfrm, 'dragover', (e) => { if (e.dataTransfer.types.indexOf('albthm')>0) { Meedya._pd(e);e.dataTransfer.dropEffect = 'move'; } });
		Meedya._ae(albfrm, 'dragenter', (e) => { if (e.dataTransfer.types.indexOf('albthm')>0) { Meedya._pd(e);e.dataTransfer.dropEffect = 'move'; } });
		Meedya._ae(albfrm, 'drop', (e) => { Meedya._pd(e); _removeAlbThm(); });
	};

	Meedya.deleteSelected = (e) => {
		Meedya._pd(e);
		if (_hasSelections("[name='slctimg[]']:checked", true)) {
			My_bb.confirm({
				message: Meedya._T('COM_MEEDYA_PERM_DELETE'),
				size: 'modal-lg',
				buttons: {
					confirm: { label: Meedya._T('JACTION_DELETE'), className: 'btn-danger' },
					cancel: { label: Meedya._T('JCANCEL') }
				},
				callback: (c) => {
					if (c) {
						document.adminForm.task.value = 'manage.deleteItems';
						document.adminForm.submit();
					}
				}
			});
		}
	};

	Meedya.removeSelected = (e) => {
		Meedya._pd(e);
		if (_hasSelections("[name='slctimg[]']:checked", true)) {
			My_bb.confirm({
				message: Meedya._T('COM_MEEDYA_REMOVE'),
				buttons: {
					confirm: { label: Meedya._T('COM_MEEDYA_VRB_REMOVE'), className: 'btn-danger' },
					cancel: { label: Meedya._T('JCANCEL') }
				},
				callback: (c) => {
					if (c) {
						let items = document.querySelectorAll("[name='slctimg[]']:checked");
						let pnode = items[0].parentNode.parentNode;
						for (let i=0; i<items.length; i++) {
							pnode.removeChild(items[i].parentNode);
						}
						thmsDirty = true;
					}
				}
			});
		}
	};

	Meedya.selAllImg = (e, X) => {
		Meedya._pd(e);
		let ck = X?'checked':'';
		let xbs = document.adminForm.elements["slctimg[]"];
		// make up for no array returned if there is only one item
		if (!xbs.length) xbs = [xbs];
		for (let i = 0; i < xbs.length; i++) {
			xbs[i].checked = ck;
		}
	};

	Meedya.editSelected = (e) => {
		Meedya._pd(e);
		if (_hasSelections("input[name='slctimg[]']:checked",true)) {
			document.adminForm.task.value = 'manage.imgsEdit';
			document.adminForm.submit();
		}
	};

	Meedya.addSelected = (e) => {
		Meedya._pd(e);
		if (_hasSelections("input[name='slctimg[]']:checked",true)) {
			openMdl('add2albdlg');
		}
	};

	Meedya.albAction = (e) => {
		let elm = e.target;		//console.log(elm);
		switch (elm.className) {
			case 'icon-edit':
				Meedya._pd(e);
				let alb2edit = elm.parentElement.dataset.aid;
				w.location = my.aURL + 'manage.editAlbum&aid=' + alb2edit;
				break;
			case 'icon-upload':
				Meedya._pd(e);
				let alb2upld = elm.parentElement.dataset.aid;
				w.location = my.aURL + 'manage.doUpload&aid=' + alb2upld;
				break;
			case 'icon-delete':
				Meedya._pd(e);
				Meedya.alb2delete = elm.parentElement.dataset.aid;
				openMdl('delact');
				break;
			case 'album':
				Meedya.AArrange.iSelect(e, elm);
				break;
		}
	};

	Meedya.saveAlbum = () => {
		if (thmsDirty) document.albForm.thmord.value = Meedya.Arrange.iord();
		document.albForm.submit();
	};

	Meedya.deleteAlbum = (elm) => {
		let frm = document.forms.dalbform;
		frm.aid.value = Meedya.alb2delete;
		frm.submit();
	};

	// watch for selection of album; enable create button when there is one
	Meedya.watchAlb = (elm) => {
		let creab = Meedya._id('creab');
		if (elm.value > 0) {
			Meedya._id('creanualb').style.display = "none";
			creab.disabled = false;
		} else {
			creab.disabled = true;
			if (elm.value == -1) {
				Meedya._id('creanualb').style.display = "block";
			} else {
				Meedya._id('creanualb').style.display = "none";
			}
		}
	};

	// watch for entry of album name; enable create button when there is a name
	Meedya.watchAlbNam = (elm) => {
		let creab = Meedya._id('creab');
		if (elm.value.trim()) {
			creab.disabled = false;
		} else {
			creab.disabled = true;
		}
	};

	Meedya.addItems2Album = (elm) => {
		elm.disabled = true;
		document.adminForm.albumid.value = Meedya._id('h5u_album').value;
		document.adminForm.nualbnam.value = Meedya._id('nualbnam').value;
		document.adminForm.nualbpar.value = Meedya._id('h5u_palbum').value;
		document.adminForm.nualbdesc.value = Meedya._id('albdesc').value;
		document.adminForm.task.value = 'manage.addItemsToAlbum';
		document.adminForm.submit();
	};

	// request creation of new album
	Meedya.ae_createAlbum = (elm) => {
		elm.disabled = true;
		let albNamFld = Meedya._id('nualbnam');
		let albParFld = Meedya._id('h5u_palbum');
		let albDscFld = Meedya._id('albdesc');
		let nualbnam = albNamFld.value.trim();
		let ajd = {albnam: nualbnam, paralb: (albParFld ? albParFld.value : 0), albdesc: albDscFld.value};
		ajd[formTokn] = 1;
		postAction('manage.newAlbum', ajd, (data) => {if (data) alert(data); else w.location.reload(true);});
	};

	// rearrange items in an album
	let moving = null;
	Meedya.moveItem = (e, elm) => {
		Meedya._pd(e, true);
		let item = elm.parentElement;
		if (!moving) {
			moving = item;
			item.classList.add("moving");
		} else {
			moving.classList.remove("moving");
			if (item != moving) {
				let area = Meedya._id('area');
				let orf = area.removeChild(moving);
				area.insertBefore(orf, item);
			}
			moving = null;
		}
	};

	// create a thumbnail for a video from the video position
	Meedya.setVideoThumb = (e, iid) => {
		Meedya._pd(e, true);
		let _CANVAS = document.getElementById("my-video-canvas"),
			_CTX = _CANVAS.getContext("2d"),
			_VIDEO = document.querySelector("#zoom-zvid"),
			_VIDOVR = document.getElementById("my-vidover"),
			_sx = 0, _sy = 0,
			_sw = _VIDEO.videoWidth,
			_sh = _VIDEO.videoHeight,
			_dx = 0, _dy = 0,
			_dw = 160,
			_dh = 0,
			_sr = 0,
			_ow = _VIDOVR.width,
			_oh = _VIDOVR.height
			;

		if (!_VIDEO) {
			alert("Did not locate the video.");
			return;
		}

		// calculate rects
		if (iid>0) {	 //always
			_dh = 160;
			_sr = Math.min(_sh/_dh, _sw/_dw);
			_sw = Math.round(_dw * _sr);
			_sh = Math.round(_dh * _sr);
			_sx = (_VIDEO.videoWidth - _sw) > 0 ? (_VIDEO.videoWidth - _sw) >> 1 : 0;
		} else {
			_sr = _sw / _sh;
			_dh = Math.round(_dw / _sr);
		}

		_CANVAS.width = _dw;
		_CANVAS.height = _dh;
		_CTX.drawImage(_VIDEO, _sx, _sy, _sw, _sh, _dx, _dy, _dw, _dh);
		_CTX.drawImage(_VIDOVR, 0, 0, _ow, _oh, 3, 3, _ow, _oh);

		let dataURL = _CANVAS.toDataURL('image/jpeg', 0.8);
		let ajd = {vid: iid, imgBase64: dataURL};
		ajd[formTokn] = 1;
		postAction('manage.setVideoThumb', ajd, (data) => { Meedya.thmelmsrc.src = data; Meedya.Zoom.close(); });
	};

	let thmsDirty = false;
	Meedya.dirtyThumbs = (v) => {thmsDirty = v};

})(window.Meedya = window.Meedya || {}, Joomla.getOptions('Meedya'), window);


// iZoom action to expand individual item
(function (mdya) {
	let back, area;

	let close = (e) => {
		e && Meedya._pd(e);
		document.body.removeChild(back);
	};

	let keyed = (e) => {
		let kcc = e.type == 'keydown' ? e.keyCode : e.charCode;
		switch (kcc) {
			case 32:
			case 13:
			case 27:
				close(e);
				break;
			default:
				break;
		}
	};

	let open = (pID, elm) => {
		if (elm) mdya.thmelmsrc = elm.parentElement.previousElementSibling.firstElementChild;
		area = document.createElement('div');
		mdya.postAction('manage.getZoomItem', {iid: pID}, (data) => { area.innerHTML = data });
		area.className = 'zoom-area';
		area.tabIndex = "-1";
		back = document.createElement('div');
		back.className = 'zoom-back';
		back.appendChild(area);
		document.body.appendChild(back);
		Meedya._ae(area, 'keypress', keyed);
		Meedya._ae(area, 'keydown', keyed);
		area.focus();
		Meedya._ae(back, 'click', close);
	};

	mdya.Zoom = {open: open, close: close};

})(Meedya);

