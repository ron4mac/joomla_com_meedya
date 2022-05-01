/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
/* globals Joomla,bootbox,iZoomClose,jQuery */
'use strict';
/* a few utility functions to avoid using jquery and assist in minification */
// getElementById
const _id = (id) => {
	return document.getElementById(id);
};
// simplify cancelling an event
const _pd = (e, sp=true) => {
	if (e.preventDefault) { e.preventDefault(); }
	if (sp && e.stopPropagation) { e.stopPropagation(); }
};
// addEventListener
const _ae = (elem, evnt, func, capt=false) => {
	elem.addEventListener(evnt, func, capt);
};
// get joomla text
const _T = (txt) => Joomla.Text._(txt);



(function (Meedya, my, $, w) {

	// establish some common variables
	const formTokn = Joomla.getOptions('csrf.token');

	const _removeAlbThm = () => {
		_id('albthmimg').src = 'components/com_meedya/static/img/img.png';
		_id('albthmid').value = 0;
	};

	const _handleAlbthmDragOver = (e) => {
		if (e.dataTransfer.types.indexOf('imgsrc') < 0) return;
		_pd(e);
		return false;
	};

	const _handleAlbthmDrop = (e) => {
		_pd(e);
		let src = e.dataTransfer.getData('imgsrc');
		if (src) {
			let aimg = e.target.parentElement.getElementsByTagName("IMG")[0];
			aimg.src = src;
			aimg.style.opacity = null;
			let atv = _id('albthmid');
			atv.value = e.dataTransfer.getData('meeid');
		}
	};

	const _hasSelections = (sel, alrt=false) => {
		if (document.querySelectorAll(sel).length) {
			return true;
		} else {
			if (alrt) bootbox.alert(_T('COM_MEEDYA_SELECT_SOME'));
			return false;
		}
	};

	//build a FormData object
	const toFormData = (obj) => {
		const formData = new FormData();
		Object.keys(obj).forEach(key => {
			if (typeof obj[key] !== 'object') formData.append(key, obj[key]);
			else formData.append(key, JSON.stringify(obj[key]));
		});
		return formData;
	};

	const postAction = (task, parms={}, cb=null, json=false, fini=null) => {
		if (typeof parms === 'object') {
			if (!(parms instanceof FormData)) parms = toFormData(parms);
		} else if (typeof parms === 'string') {
			parms = new URLSearchParams(parms);
		}
		if (task) parms.set('task', task);
	
		fetch(my.rawURL, {method:'POST', body:parms})
		.then(resp => { if (!resp.ok) throw new Error(`HTTP ${resp.status}`); if (json) return resp.json(); else return resp.text() })
		.then(data => cb && cb(data))
		.catch(err => alert('Failure: '+err))
		.then(()=>fini && fini());
	};

	// open or close modals whether on J4 or J3 bootstrap
	const openMdl = (elm) => { elm.open ? elm.open() : jQuery(elm).modal('show'); };
	const closMdl = (elm) => { elm.close ? elm.close() : jQuery(elm).modal('hide'); };


//@@@@@@@@@@ PUBLIC FUNCTIONS @@@@@@@@@@

	// we'll export the postAction
	Meedya.postAction = postAction;

	// utility dialog actions for alert, confirm using bootstrap modals
	let yescb = null;
	Meedya.confirm = (dlg, titl, body, cb) => {
		//set the title and body 
		$('#'+dlg+' .modal-title').html(titl);
		$('#'+dlg+' .modal-body').html(body);
		yescb = cb;
		$('#'+dlg).modal('show');
	};
	Meedya.confirmed = (y) => yescb(y);
	Meedya.alert = (body) => {
		//set the body 
		$('#alert-dlg span').html(body);
		$('#alert-dlg').show();
	};

	Meedya.setAlbumDanD = () => {
		let albthm = _id("albthm");
		_ae(albthm, 'dragover', _handleAlbthmDragOver);
		_ae(albthm, 'drop', _handleAlbthmDrop);
		_ae(albthm, 'dragenter', (e) => { _pd(e); e.target.style.opacity = '0.5'; });
		_ae(albthm, 'dragleave', (e) => e.target.style.opacity = null);
		let albfrm = _id("albForm");
		_ae(albfrm, 'dragstart', (e) => e.dataTransfer.setData('albthm','X'));
		_ae(albfrm, 'dragover', (e) => { if (e.dataTransfer.types.indexOf('albthm')>0) { _pd(e);e.dataTransfer.dropEffect = 'move'; } });
		_ae(albfrm, 'dragenter', (e) => { if (e.dataTransfer.types.indexOf('albthm')>0) { _pd(e);e.dataTransfer.dropEffect = 'move'; } });
		_ae(albfrm, 'drop', (e) => { _pd(e); _removeAlbThm(); });
	};

	Meedya.deleteSelected = (e) => {
		_pd(e);
		if (_hasSelections("[name='slctimg[]']:checked", true)) {
			bootbox.confirm({
				message: _T('COM_MEEDYA_PERM_DELETE'),
				buttons: {
					confirm: { label: _T('JACTION_DELETE'), className: 'btn-danger' },
					cancel: { label: _T('JCANCEL') }
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
		_pd(e);
		if (_hasSelections("[name='slctimg[]']:checked", true)) {
/*			bootbox.prompt({
				title: "Remove from album ...",
				message: _T('COM_MEEDYA_REMOVE')+'<br><br>',
				inputType: 'checkbox',
				inputOptions: [{
					text: 'Totally delete from every album',
					value: '1',
				}],
				buttons: {
					confirm: {
						label: 'Remove',
						className: 'btn-warning'
					}
				},
				callback: function (result) {
					console.log(result);
					if (result == null) return;
					alert(result.length?'delete':'remove');
				}
			});*/
			bootbox.confirm({
				message: _T('COM_MEEDYA_REMOVE'),
				buttons: {
					confirm: { label: _T('COM_MEEDYA_VRB_REMOVE'), className: 'btn-danger' },
					cancel: { label: _T('JCANCEL') }
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
		_pd(e);
		let ck = X?'checked':'';
		let xbs = document.adminForm.elements["slctimg[]"];
		// make up for no array returned if there is only one item
		if (!xbs.length) xbs = [xbs];
		for (let i = 0; i < xbs.length; i++) {
			xbs[i].checked = ck;
		}
	};

	Meedya.editSelected = (e) => {
		_pd(e);
		if (_hasSelections("input[name='slctimg[]']:checked",true)) {
			document.adminForm.task.value = 'manage.imgsEdit';
			document.adminForm.submit();
		}
	};

	Meedya.addSelected = (e) => {
		_pd(e);
		if (_hasSelections("input[name='slctimg[]']:checked",true)) {
			openMdl(_id('add2albdlg'));
		}
	};

	Meedya.albAction = (e) => {
		let clkd = e.target;		//console.log(clkd);
		switch (clkd.className) {
			case 'icon-edit':
				albEdtAction(e, clkd);
				break;
			case 'icon-upload':
				albUpldAction(e, clkd);
				break;
			case 'icon-delete':
				albDelAction(e, clkd);
				break;
			case 'album':
				Meedya.AArrange.iSelect(e, clkd);
				break;
		}
	};

	Meedya.saveAlbum = () => {
		if (thmsDirty) document.albForm.thmord.value = Meedya.Arrange.iord();
		document.albForm.submit();
	};

	let albEdtAction = (e, elm) => {
		_pd(e);
		let alb2edit = elm.parentElement.dataset.aid;
		window.location = my.aURL + 'manage.editAlbum&aid=' + alb2edit;
	};

	let albUpldAction = (e, elm) => {
		_pd(e);
		let alb2upld = elm.parentElement.dataset.aid;
		window.location = my.aURL + 'manage.doUpload&aid=' + alb2upld;
	};

	let albDelAction = (e, elm) => {
		_pd(e);
		Meedya.alb2delete = elm.parentElement.dataset.aid;
		openMdl(_id('delact'));
	};

	Meedya.deleteAlbum = (elm) => {
		let frm = document.forms.dalbform;
		frm.aid.value = Meedya.alb2delete;
		frm.submit();
	};

	// watch for selection of album; enable create button when there is one
	Meedya.watchAlb = (elm) => {
		let creab = _id('creab');
		let classes = creab.classList;
		if (elm.value > 0) {
			_id('creanualb').style.display = "none";
		//	classes.remove("btn-disabled");
		//	classes.add("btn-primary");
			creab.disabled = false;
		} else {
		//	classes.remove("btn-primary");
		//	classes.add("btn-disabled");
			creab.disabled = true;
			if (elm.value == -1) {
				_id('creanualb').style.display = "block";
			} else {
				_id('creanualb').style.display = "none";
			}
		}
	};

	// watch for entry of album name; enable create button when there is a name
	Meedya.watchAlbNam = (elm) => {
		let creab = _id('creab');
		let classes = creab.classList;
		if (elm.value.trim()) {
		//	classes.remove("btn-disabled");
		//	classes.add("btn-secondary");
			creab.disabled = false;
		} else {
		//	classes.remove("btn-secondary");
		//	classes.add("btn-disabled");
			creab.disabled = true;
		}
	};

	Meedya.addItems2Album = (elm) => {
		elm.disabled = true;
		document.adminForm.albumid.value = _id('h5u_album').value;
		document.adminForm.nualbnam.value = _id('nualbnam').value;
		document.adminForm.nualbpar.value = _id('h5u_palbum').value;
		document.adminForm.nualbdesc.value = _id('albdesc').value;
		document.adminForm.task.value = 'manage.addItemsToAlbum';
		document.adminForm.submit();
	};

	// request creation of new album
	Meedya.ae_createAlbum = (elm) => {
		elm.disabled = true;
		let albNamFld = _id('nualbnam');
		let albParFld = _id('h5u_palbum');
		let albDscFld = _id('albdesc');
		let nualbnam = albNamFld.value.trim();
		let ajd = {task: 'manage.newAlbum', albnam: nualbnam, paralb: (albParFld ? albParFld.value : 0), albdesc: albDscFld.value};
		ajd[formTokn] = 1;
		postAction(null, ajd, (data) => {if (data) alert(data); else w.location.reload(true);});
	};

	// rearrange items in an album
	let moving = null;
	Meedya.moveItem = (e, elm) => {
		_pd(e, true);
		let item = elm.parentElement;
		if (!moving) {
			moving = item;
			item.classList.add("moving");
		} else {
			moving.classList.remove("moving");
			if (item != moving) {
				let area = _id('area');
				let orf = area.removeChild(moving);
				area.insertBefore(orf, item);
			}
			moving = null;
		}
	};

	// create a thumbnail for a video from the video position
	Meedya.setVideoThumb = (e, iid) => {
		_pd(e, true);
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
		let ajd = {task: 'manage.setVideoThumb', vid: iid, imgBase64: dataURL};
		ajd[formTokn] = 1;
		postAction(null, ajd, (data) => { Meedya.thmelmsrc.src = data; iZoomClose(); });
	};

	let thmsDirty = false;
	Meedya.dirtyThumbs = (v) => {thmsDirty = v};

})(window.Meedya = window.Meedya || {}, Joomla.getOptions('Meedya'), jQuery, window);


// iZoom action to expand individual item
(function (mdya, my, $, w) {
	let back, area;

	let close = (e) => {
		e && _pd(e);
		document.body.removeChild(back);
	};

	let keyPressed = (e) => {
		switch (e.charCode) {
			case 32:
			case 13:
			case 27:
				close(e);
				break;
			default:
				break;
		}
	};

	let keyDowned = (e) => {
		switch (e.keyCode) {
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
		_ae(area, 'keypress', keyPressed);
		_ae(area, 'keydown', keyDowned);
		area.focus();
		_ae(back, 'click', close);
	};

	w.iZoomOpen = open;
	w.iZoomClose = close;

})(Meedya, Joomla.getOptions('Meedya'), jQuery, window);

