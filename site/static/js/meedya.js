/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
/* jshint esnext:false, esversion:9 */
/* globals Joomla,jQuery,Fancybox,ssCtl,SimpleStarRating,newcmnt */
'use strict';

// creates or adds to a namespace, 'Meedya'

(function(Meedya, my, $) {

	// -------------------------------------------------- local variables
	let token,
		self = Meedya,
		rDlg,
		ssr,
		curRelm,
		curIid = 0,
		curCelm,
		cDlg,
		ncDlg,
		cElm;

	// -------------------------------------------------- private functions
	/** @noinline */
	const _id = id => document.getElementById(id);

	const viewer = {
		// video player options
		vopts: {
			video: {
				tpl:
					'<video class="fancybox-video" controls controlsList="nodownload" poster="{{poster}}" playsinline >' +
					'<source src="{{src}}" type="{{format}}" />' +
					'Sorry, your browser does not support embedded videos, <a href="{{src}}">download</a> and watch with your favorite video player!' +
					"</video>",
				autoStart: true
			}
		},
		// standard options
		sopts: {
			loop: false,
			slideShow: {speed: 5000},
		},
		// image view buttons
		ivbuts: {
			buttons: ["zoom","slideShow","fullScreen","download","close"]
		},
		// slideshow buttons
		ssbuts: {
			buttons: ["fullScreen","close"],
			slideShow: {autoStart: true}
		},
		// the following functions need to make copies of the itemslist
		// to make possible multiple invocations on the same page (without reload)
		showSlide: (e, iid) => {
			e.preventDefault();
			let imgl = JSON.parse(JSON.stringify(self.items));	//copy
			if (Meedya.FB4) {
				Fancybox.show(imgl,{startIndex: iid});
			} else {
				$.fancybox.open(imgl, {...this.sopts, ...this.vopts, ...this.ivbuts}, iid);
			}
		},
		slideShow: (e) => {
			e.preventDefault();
			let imgl = JSON.parse(JSON.stringify(Meedya.items));	//copy
			if (Meedya.FB4) {
				Fancybox.show(imgl/*,{mainClass: "mdyFancybox"}*/);
			} else {
				$.fancybox.open(imgl, {...this.sopts, ...this.vopts, ...this.ssbuts});
			}
		}
	};

	const old_viewer = {
		showSlide: (e, iid) => {
			e.preventDefault();
			$('#sstage').appendTo('body').show();
			ssCtl.init(Meedya.items, iid);
		}
	};

	// open or close modals based on J4 or J3 bootstrap
	const openMdl = (elm) => { elm.open ? elm.open() : jQuery(elm).modal('show'); };
	const closMdl = (elm) => { elm.close ? elm.close() : jQuery(elm).modal('hide'); };

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

	const dorate = (itemid, relm) => {
		curIid = itemid;
		curRelm = relm;
		postAction('rateChk', {iid: itemid}, (rsp) => { if (rsp) alert(rsp); else openMdl(rDlg); });
	};

	const fetchComments = (itemid) => {
		curIid = itemid;
		cElm.innerHTML = '';
		postAction('getComments', {[token]: 1, iid: itemid}, (data) => { cElm.innerHTML = data; openMdl(cDlg); });
	};

	const doComments = (itemid, elm) => {
		curIid = itemid;
		curCelm = elm;
		if (elm.classList.contains('hasem')) {
			fetchComments(itemid);
		} else {
			_id("cmnt-text").value = "";
			openMdl(ncDlg);
		}
	};

	const submitRating = (evt) => {
		if (evt.detail === 0) {
			if (!confirm("Clear rating for this item?")) return;
		}
		postAction('rateItem', {[token]: 1, iid: curIid, val: evt.detail}, (data)=>{
			curRelm.firstElementChild.firstElementChild.style.width = data+"%";
		//	closMdl(rDlg);
			ssr.enable();
		}, false, ()=>closMdl(rDlg));
	};

	const _t = (tid) => {
		return Meedya.L[tid] ? Meedya.L[tid] : tid;
	};


	// -------------------------------------------------- public functions
	
	Meedya.initIV = (old=false) => {
		Meedya.viewer = old ? old_viewer : viewer;
	};

	Meedya.performSearch = (aform) => {
		var sterm = aform.sterm.value.trim();
		if (sterm==='') {
			alert(self.L.no_sterm);
			return false;
		}
		aform.submit();
		return false;
	};

	Meedya.thmClick = (e) => {
		let clkd = e.target;
		let iid = 0;
		switch (clkd.className) {
			case 'imgthm':
				e.stopPropagation();
				viewer.showSlide(e, +clkd.parentElement.parentElement.parentElement.dataset.ix);
				break;
			case 'strating':
				clkd = clkd.parentElement;
				/* falls through */
			case 'strback':
				clkd = clkd.parentElement;
				/* falls through */
			case 'strate':
				e.stopPropagation();
				if (!rDlg) break;
				iid = +clkd.parentElement.parentElement.parentElement.parentElement.dataset.iid;
				dorate(iid, clkd);
				break;
			case 'far fa-comments':
			case 'icon-comments-2':			// J3
				clkd = clkd.parentElement;
				/* falls through */
			case 'mycmnts':
			case 'mycmnts hasem':
				e.stopPropagation();
				iid = +clkd.parentElement.parentElement.parentElement.parentElement.dataset.iid;
				doComments(iid, clkd);
				break;
		}
	};

	Meedya.submitComment = (elm) => {
		elm.disabled = true;
		let fData = new FormData(newcmnt);
		fData.append('iid', curIid);
		postAction(null, fData, (data) => {
			closMdl(ncDlg);
			elm.disabled = false;
			curCelm.classList.add('hasem');
			curCelm.innerHTML = data;
		});
	};

	// CURRENTLY UNUSED
	Meedya.sprintf = (format) => {
		for (var i = 1; i < arguments.length; i++) {
			format = format.replace( /%s/, arguments[i] );
		}
		return format;
	};

	document.addEventListener('DOMContentLoaded', () => {
		// get the joomla suplied csrf token
		token = Joomla.getOptions('csrf.token', '');
		// setup the star rating modal
		rDlg = _id('rating-modal');
		if (rDlg) {
			rDlg.addEventListener('hidden.bs.modal', (event) => {closMdl(rDlg);});		// NOT SURE ABOUT THIS
			var rating = _id('unrating');
			ssr = new SimpleStarRating(rating);
			rating.addEventListener('rate', submitRating);
		}
		// setup comments display modal
		cDlg = _id('comments-modal');
		if (cDlg) cElm = document.querySelector(".modal-body .comments");
		// setup new comment entry modal
		ncDlg = _id('comment-modal');
		if (ncDlg) {
	//		ncElm = document.querySelector(".modal-body textarea");
			// focus on the textarea
			ncDlg.addEventListener('shown.bs.modal', (event) => {_id("cmnt-text").focus();});
		}
	});

})(window.Meedya = window.Meedya || {}, Joomla.getOptions('Meedya'), jQuery);
