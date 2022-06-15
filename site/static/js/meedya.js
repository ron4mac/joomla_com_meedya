/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
/* jshint esnext:false, esversion:9 */
/* globals Joomla,jQuery,Fancybox,ssCtl,SimpleStarRating,newcmnt */
'use strict';

// creates or adds to a namespace, 'Meedya'

(function(Meedya, my) {

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
			Meedya._pd(e);
			let imgl = JSON.parse(JSON.stringify(self.items));	//copy
			if (Meedya.FB4) {
				Fancybox.show(imgl,{startIndex: iid});
			} else {
				jQuery.fancybox.open(imgl, {...this.sopts, ...this.vopts, ...this.ivbuts}, iid);
			}
		},
		slideShow: (e) => {
			Meedya._pd(e);
			let imgl = JSON.parse(JSON.stringify(Meedya.items));	//copy
			if (Meedya.FB4) {
				Fancybox.show(imgl/*,{mainClass: "mdyFancybox"}*/);
			} else {
				jQuery.fancybox.open(imgl, {...this.sopts, ...this.vopts, ...this.ssbuts});
			}
		}
	};

	const old_viewer = {
		showSlide: (e, iid) => {
			Meedya._pd(e);
			jQuery('#sstage').appendTo('body').show();
			ssCtl.init(Meedya.items, iid);
		}
	};

	// shortcuts to common Meedya functions
	const openMdl = Meedya._oM;
	const closMdl = Meedya._cM;
	const postAction = Meedya._P;

	// handle user request to enter an item rating
	const dorate = (evt, itemid, relm) => {
		curIid = itemid;
		curRelm = relm;
		if (my.isAdmin && evt.metaKey) openMdl(rDlg);	// allow gallery admin to force with cmd/alt
		else postAction('rateChk', {iid: itemid}, (rsp) => { if (rsp) alert(rsp); else openMdl(rDlg); });
	};

	// get and display the comments for an item
	const fetchComments = (itemid) => {
		curIid = itemid;
		cElm.innerHTML = '';
		postAction('getComments', {[token]: 1, iid: itemid}, (data) => { cElm.innerHTML = data; openMdl(cDlg); });
	};

	// either display current comments, if any, or provide dialog for new comment 
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

	// send rating value to server
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

//	const _t = (tid) => {
//		return Meedya.L[tid] ? Meedya.L[tid] : tid;
//	};


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
				dorate(e, iid, clkd);
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
//	Meedya.sprintf = (format) => {
//		for (var i = 1; i < arguments.length; i++) {
//			format = format.replace( /%s/, arguments[i] );
//		}
//		return format;
//	};

	Meedya._ae(document, 'DOMContentLoaded', () => {
		// get the joomla suplied csrf token
		token = Joomla.getOptions('csrf.token', '');
		// setup the star rating modal
		rDlg = _id('rating-modal');
		if (rDlg) {
			Meedya._ae(rDlg, 'hidden.bs.modal', (event) => {closMdl(rDlg);});		// NOT SURE ABOUT THIS
			var rating = _id('unrating');
			ssr = new SimpleStarRating(rating);
			Meedya._ae(rating, 'rate', submitRating);
		}
		// setup comments display modal
		cDlg = _id('comments-modal');
		if (cDlg) cElm = document.querySelector(".modal-body .comments");
		// setup new comment entry modal
		ncDlg = _id('comment-modal');
		if (ncDlg) {
	//		ncElm = document.querySelector(".modal-body textarea");
			// focus on the textarea
			Meedya._ae(ncDlg, 'shown.bs.modal', (event) => {Meedya._id("cmnt-text").focus();});
		}
	});

})(window.Meedya = window.Meedya || {}, Joomla.getOptions('Meedya'));
