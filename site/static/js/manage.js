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



var Meedya = (function (my, $, w) {

	// establish some common variables
	const formTokn = Joomla.getOptions('csrf.token');
	let thmsDirty = false;

	let _removeAlbThm = () => {
		_id('albthmimg').src = 'components/com_meedya/static/img/img.png';
		_id('albthmid').value = 0;
	};

	let _handleAlbthmDragOver = (e) => {
		if (e.dataTransfer.types.indexOf('imgsrc') < 0) return;
		_pd(e);
		return false;
	};

	let _handleAlbthmDrop = (e) => {
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

	let _hasSelections = (sel, alrt=false) => {
		if (document.querySelectorAll(sel).length) {
			return true;
		} else {
			if (alrt) bootbox.alert(_T('COM_MEEDYA_SELECT_SOME'));
			return false;
		}
	};


//@@@@@@@@@@ PUBLIC FUNCTIONS @@@@@@@@@@

	// utility dialog actions for alert, confirm using bootstrap modals
	let yescb = null;
	let confirm = (dlg, titl, body, cb) => {
		//set the title and body 
		$('#'+dlg+' .modal-title').html(titl);
		$('#'+dlg+' .modal-body').html(body);
		yescb = cb;
		$('#'+dlg).modal('show');
	};
	let confirmed = (y) => yescb(y);
	let alert = (body) => {
		//set the body 
		$('#alert-dlg span').html(body);
		$('#alert-dlg').show();
	};

	let setDlgParAlb = () => {
		if (_id('h5u_palbum'))
		_id('h5u_palbum').value = Meedya.AArrange.selalb();
	};

	let setAlbumDanD = () => {
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

	let deleteSelected = (e) => {
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

	let removeSelected = (e) => {
		_pd(e);
		if (_hasSelections("[name='slctimg[]']:checked", true)) {
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

	let selAllImg = (e, X) => {
		_pd(e);
		let ck = X?'checked':'';
		let xbs = document.adminForm.elements["slctimg[]"];
		// make up for no array returned if there is only one item
		if (!xbs.length) xbs = [xbs];
		for (let i = 0; i < xbs.length; i++) {
			xbs[i].checked = ck;
		}
	};

	let editSelected = (e) => {
		_pd(e);
		if (_hasSelections("input[name='slctimg[]']:checked",true)) {
			document.adminForm.task.value = 'manage.imgsEdit';
			document.adminForm.submit();
		}
	};

	let addSelected = (e) => {
		_pd(e);
		if (_hasSelections("input[name='slctimg[]']:checked",true)) {
			$('#add2albdlg').modal('show');
		}
	};

	let saveAlbum = () => {
		if (thmsDirty) document.albForm.thmord.value = Meedya.Arrange.iord();
		document.albForm.submit();
	};

	// watch for selection of album; enable create button when there is one
	let watchAlb = (elm) => {
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
	let watchAlbNam = (elm) => {
		//var creab = _id('creab');	console.log(creab,elm.value);
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

	let addItems2Album = (elm) => {
		elm.disabled = true;
		document.adminForm.albumid.value = _id('h5u_album').value;
		document.adminForm.nualbnam.value = _id('nualbnam').value;
		document.adminForm.nualbpar.value = _id('h5u_palbum').value;
		document.adminForm.nualbdesc.value = _id('albdesc').value;
		document.adminForm.task.value = 'manage.addItemsToAlbum';
		document.adminForm.submit();
	};

	let NOT_USED_aj_addItems2Album = (elm) => {
		elm.disabled = true;
		var albNamFld = _id('nualbnam');
		var albParFld = _id('h5u_palbum');
		var albDscFld = _id('albdesc');
		var nualbnam = albNamFld.value.trim();
		var ajd = {task: 'manage.addItemsToAlbum', albnam: nualbnam, paralb: (albParFld ? albParFld.value : 0), albdesc: albDscFld.value};
		ajd[formTokn] = 1;
		$.post(my.rawURL, ajd,
			function (response, status, xhr) {
				//console.log(response, status, xhr);
				if (status=="success") {
					if (response) {
						alert(response);
					} else {
						window.location.reload(true);
					}
				} else {
					alert(xhr.statusText);
				}
				elm.disabled = false;
			}
		);
	};

	// request creation of new album
	let ae_createAlbum = (elm) => {
		elm.disabled = true;
		let albNamFld = _id('nualbnam');
		let albParFld = _id('h5u_palbum');
		let albDscFld = _id('albdesc');
		let nualbnam = albNamFld.value.trim();
		let ajd = {task: 'manage.newAlbum', albnam: nualbnam, paralb: (albParFld ? albParFld.value : 0), albdesc: albDscFld.value};
		ajd[formTokn] = 1;
		$.post(my.rawURL, ajd)
		.done(data => {if (data) alert(data); else w.location.reload(true);})
		.fail(rsp => alert(rsp.responseText));
	};

	// rearrange items in an album
	let moving = null;
	let moveItem = (e, elm) => {
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
	let setVideoThumb = (e, iid) => {
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
		ajd[Joomla.getOptions('csrf.token', '')] = 1;
		$.post(my.rawURL, ajd)
		.done((data) => { Meedya.thmelmsrc.src = data; iZoomClose(); })
		.fail((rsp) => alert(rsp.responseText));
	};

	let dirtyThumbs = (v) => {thmsDirty = v};

	// return the Meedya public functions
	return {
		confirm: confirm,
		confirmed: confirmed,
		alert: alert,
		setDlgParAlb: setDlgParAlb,
		selAllImg: selAllImg,
		editSelected: editSelected,
		addSelected: addSelected,
		removeSelected: removeSelected,
		deleteSelected: deleteSelected,
		setAlbumDanD: setAlbumDanD,
		saveAlbum: saveAlbum,
		watchAlb: watchAlb,
		watchAlbNam: watchAlbNam,
	//	aj_addItems2Album: aj_addItems2Album,
		addItems2Album: addItems2Album,
		ae_createAlbum: ae_createAlbum,
		moveItem: moveItem,
		setVideoThumb: setVideoThumb,
		dirtyThumbs: dirtyThumbs
	};
})(Joomla.getOptions('Meedya'), jQuery, window);


Meedya.Arrange = (function ($) {
	let dragSrcEl = null,
		iSlctd = null,
		stop = true,
		ctnr = '',
		clas = '',
		meeid = 'meeid',
		items;

	// Private functions
	let dropable = (e) => {
		if (e.target.parentElement.parentElement == dragSrcEl) return false;
		let typs = e.dataTransfer.types;
		for (let i = 0; i < typs.length; ++i ) {
			if (typs[i] === meeid) return true;
		}
		return false;
	};

	let handleDragStart = (e) => {
		dragSrcEl = e.target.parentElement.parentElement;
		dragSrcEl.style.opacity = '0.4';
		e.dataTransfer.effectAllowed = 'copyMove';
		e.dataTransfer.setData(meeid,dragSrcEl.dataset.id);
		e.dataTransfer.setData('imgsrc',e.target.src);
	};

	let handleDrag = (e) => {
		stop = true;
		if (e.clientY < 50) {
			stop = false;
			scroll(-1);
		}
		if (e.clientY > ($(window).height() - 50)) {
			stop = false;
			scroll(1);
		}
	};

	let tMove = (e) => {
		if (e.targetTouches.length == 1) {
			let touch = e.targetTouches[0];
			// Place element where the finger is
			e.target.style.left = touch.pageX + 'px';
			e.target.style.top = touch.pageY + 'px';
		}
	};

	let handleDragEnd = (e) => {
		dragSrcEl.style.opacity = null;
		[].forEach.call(items, (itm) => itm.classList.remove('over'));
		stop = true;
	};

	let handleDrop = (e) => {
		_pd(e);
		let dtarg = e.target.parentElement.parentElement;
		// Don't do anything if dropping the same item we're dragging.
		if (dragSrcEl != dtarg) {
			let area = _id(ctnr);
			let orf = area.removeChild(dragSrcEl);
			area.insertBefore(orf, dtarg);
			Meedya.dirtyThumbs(true);
		}
		return false;
	};

	let handleDragEnter = (e) => {
		if (dropable(e)) {
			_pd(e);
			e.target.classList.add('over');
			return false;
		}
	};

	let handleDragOver = (e) => {
		if (dropable(e)) {
			_pd(e);
			return false;
		}
	};

	let handleDragLeave = (e) => {
		e.target.classList.remove('over');
	};

	const scroll = (step) => {
		let scrollY = $(window).scrollTop();
		$(window).scrollTop(scrollY + step);
		if (!stop) {
			setTimeout(() => scroll(step), 60);
		}
	};

	// Return exported functions
	return {
		init: (iCtnr, iClass) => {
			ctnr = iCtnr;
			clas = iClass;
		//	items = document.querySelectorAll('#'+iCtnr+' .'+iClass);
			items = document.querySelectorAll('#'+iCtnr+' img');
			[].forEach.call(items, (itm) => {
			//		itm.setAttribute('draggable', 'true');
					_ae(itm, 'drag', handleDrag);
					_ae(itm, 'dragstart', handleDragStart);
					_ae(itm, 'dragenter', handleDragEnter);
					_ae(itm, 'dragover', handleDragOver);
					_ae(itm, 'dragleave', handleDragLeave);
					_ae(itm, 'drop', handleDrop);
					_ae(itm, 'dragend', handleDragEnd);
					_ae(itm, 'touchmove', tMove);
				});
		},
		iord: () => {
		//	items = document.querySelectorAll('#'+ctnr+' .'+clas);
			items = document.querySelectorAll('#'+ctnr+' .item');
			let imord = [];
			[].forEach.call(items, (itm) => {
				let iid = itm.dataset.id;
				if (iid) imord.push(iid);
			});
			return imord.join("|");
		}
	};
}(jQuery));


// Need to have a separate Drag and Drop arranger for the gallery album hierarchy

Meedya.AArrange = (function (my, $) {
	let dragSrcEl = null,
		deTarg = null,
		iSlctd = null,
		stop = true,
		ctnr = '',
		meeid = 'meeid',
		items;

	// Private functions

	let setAlbPaid = (aid, paid, func) => {
		let prms = {task: 'manage.adjustAlbPaid', 'aid': aid, 'paid': paid};
		prms[Joomla.getOptions('csrf.token', '')] = 1;
		$.post(my.rawURL, prms, (d) => func(d));
	};

	let dropable = (e) => {
		if (e.target == dragSrcEl) return false;
		let typs = e.dataTransfer.types;
		for (let i = 0; i < typs.length; ++i ) {
			if (typs[i] === meeid) return true;
		}
		return false;
	};

	let handleDragStart = (e) => {
		e.target.style.opacity = '0.4';
		dragSrcEl = e.target;
		e.dataTransfer.effectAllowed = 'copyMove';
		e.dataTransfer.setData(meeid,dragSrcEl.dataset.id);
		e.target.classList.add('moving');
	};

	let handleDragOver = (e) => {
		if (dropable(e)) {
			_pd(e);
			return false;
		}
	};

	let handleDragEnter = (e) => {
		if (dropable(e)) {
			_pd(e);
			deTarg = e.target;
			e.target.classList.add('over');
			return false;
		}
	};

	let handleDragLeave = (e) => {
//		if (e.target == deTarg) {
			_pd(e);
			e.target.classList.remove('over');
//		}
	};

	let handleDrop = (e) => {
		_pd(e);
		// Don't do anything if dropping the same item we're dragging.
		if (dragSrcEl != e.target) {
			let sa = dragSrcEl.dataset.aid;
			let da = e.target.dataset.aid;
			setAlbPaid(sa, da, (r) => {
				if (r) {
					bootbox.alert(_T('COM_MEEDYA_MOVE_FAIL'));
				} else {
					e.target.append(dragSrcEl);
				}
			});
		}
		return false;
	};

	let handleDragEnd = (e) => {
		dragSrcEl.style.opacity = null;
		[].forEach.call(items, (itm) => itm.classList.remove('over'));
		e.target.classList.remove('moving');
		stop = true;
	};

	let handleDrag = (e) => {
		stop = true;
		if (e.clientY < 50) {
			stop = false;
			scroll(-1);
		}
		if (e.clientY > ($(window).height() - 50)) {
			stop = false;
			scroll(1);
		}
	};

	let tMove = (e) => {
		if (e.targetTouches.length == 1) {
			let touch = e.targetTouches[0];
			// Place element where the finger is
			e.target.style.left = touch.pageX + 'px';
			e.target.style.top = touch.pageY + 'px';
		}
	};

	let iSelect = (e, elm=this) => {
		_pd(e);
		if (iSlctd) iSlctd.classList.remove('slctd');
		if (elm == iSlctd) {
			iSlctd = null;
		} else {
			iSlctd = elm;
			iSlctd.classList.add('slctd');
		}
	};

	const scroll = (step) => {
		let scrollY = $(window).scrollTop();
		$(window).scrollTop(scrollY + step);
		if (!stop) {
			setTimeout(() => scroll(step), 500);
		}
	};

	// Return exported functions
	return {
		init: (iCtnr, iClass) => {
			ctnr = iCtnr;
			items = document.querySelectorAll('#'+iCtnr+' .'+iClass);
			[].forEach.call(items, (itm) => {
					itm.setAttribute('draggable', 'true');
					_ae(itm, 'drag', handleDrag);
					_ae(itm, 'dragstart', handleDragStart, true);
					_ae(itm, 'dragenter', handleDragEnter);
					_ae(itm, 'dragover', handleDragOver);
					_ae(itm, 'dragleave', handleDragLeave);
					_ae(itm, 'drop', handleDrop);
					_ae(itm, 'dragend', handleDragEnd);
					_ae(itm, 'touchmove', tMove);
				//	_ae(itm, 'click', iSelect);
				});
		},
		selalb: () => {
			return iSlctd ? iSlctd.dataset.aid : 0;
		},
		iSelect: iSelect
	};
}(Joomla.getOptions('Meedya'), jQuery));


// iZoom action to expand individual item
(function (my, $, w) {
	let back, area;

	let close = (e) => {
		if (e) _pd(e);
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
		if (elm) Meedya.thmelmsrc = elm.parentElement.previousElementSibling.firstElementChild;
		area = document.createElement('div');
		$.post(my.rawURL, {task: 'manage.getZoomItem', iid: pID})
		.done(data => area.innerHTML = data);
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

})(Joomla.getOptions('Meedya'), jQuery, window);

