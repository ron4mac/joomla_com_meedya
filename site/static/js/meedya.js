/* jshint esnext:false, esversion:9 */
/* globals Joomla,jQuery,Fancybox,ssCtl,SimpleStarRating,newcmnt */
'use strict';
var Meedya = {};	// a namespace for com_meedya

(function(my, $) {

	var token, self=Meedya;

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

	Meedya.initIV = (old=false) => {
		Meedya.viewer = old ? old_viewer : viewer;
	};

	Meedya.performSearch = (aform) => {
		var sterm = $.trim(aform.sterm.value);
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
				Meedya.dorate(iid, clkd);
				break;
			case 'far fa-comments':
			case 'icon-comments-2':			// J3
				clkd = clkd.parentElement;
				/* falls through */
			case 'mycmnts':
			case 'mycmnts hasem':
				e.stopPropagation();
				iid = +clkd.parentElement.parentElement.parentElement.parentElement.dataset.iid;
				Meedya.doComments(iid, clkd);
				break;
		}
	};


	var rDlg, ssr, curRelm;

	const submitRating = (evt) => {
		if (evt.detail === 0) {
			if (!confirm("Clear rating for this item?")) return;
		}
		$.post(my.rawURL, {[token]: 1, task: 'rateItem', iid: curIid, val: evt.detail})
		.done(data => {
			curRelm.firstElementChild.firstElementChild.style.width = data+"%";
			$(rDlg).modal('hide');
			ssr.enable();
		})
		.fail(err => {
			alert(err.responseText);
			$(rDlg).modal('hide');
		});
	};

	Meedya.dorate = (itemid, relm) => {
		curIid = itemid;
		curRelm = relm;
		$.post(my.rawURL, {task: 'rateChk', iid: itemid})
		.done(() => { $(rDlg).modal('show'); })
		.fail((err) => { alert(err.responseText); });
	};


	var curIid = 0, curCelm;
	var cDlg, ncDlg, cElm;

	const fetchComments = (itemid) => {
		curIid = itemid;
		cElm.innerHTML = '';
		$.post(my.rawURL, {[token]: 1, task: 'getComments', iid: itemid})
		.done((data) => { cElm.innerHTML = data; $(cDlg).modal('show'); })
		.fail((err) => { alert(err.responseText); });
	};

	Meedya.submitComment = (elm) => {
		elm.disabled = true;
		let pdat = {iid: curIid};
		let fData = new FormData(newcmnt);
		for (let kv of fData.entries()) pdat[kv[0]] = kv[1];
		$.post(my.rawURL, pdat)
		.done((data) => {
			$(ncDlg).modal('hide');
			elm.disabled = false;
			curCelm.classList.add('hasem');
			curCelm.innerHTML = data;
		})
		.fail((err) => { alert(err.responseText); });
	};

	Meedya.doComments = (itemid, elm) => {
		curIid = itemid;
		curCelm = elm;
		if (elm.classList.contains('hasem')) {
			fetchComments(itemid);
//			$(cDlg).modal('show');
		} else {
			document.getElementById("cmnt-text").value = "";
			$(ncDlg).modal('show');
		}
	};

	// CURRENTLY UNUSED
	Meedya.sprintf = (format) => {
		for (var i = 1; i < arguments.length; i++) {
			format = format.replace( /%s/, arguments[i] );
		}
		return format;
	};

	const _t = (tid) => {
		return Meedya.L[tid] ? Meedya.L[tid] : tid;
	};

	$(document).ready( () => {
		$('['+Meedya.datatog+'="tooltip"]').tooltip();
		// get the joomla suplied csrf token
		token = Joomla.getOptions('csrf.token', '');
		// setup the star rating modal
		rDlg = document.getElementById('rating-modal');
		if (rDlg) {
			rDlg.addEventListener('hidden.bs.modal', (event) => {$(rDlg).modal('hide');});		// NOT SURE ABOUT THIS
			var rating = document.getElementById('unrating');
			ssr = new SimpleStarRating(rating);
			rating.addEventListener('rate', submitRating);
		}
		// setup comments display modal
		cDlg = document.getElementById('comments-modal');
		if (cDlg) cElm = document.querySelector(".modal-body .comments");
		// setup new comment entry modal
		ncDlg = document.getElementById('comment-modal');
		if (ncDlg) {
	//		ncElm = document.querySelector(".modal-body textarea");
			// focus on the textarea
			ncDlg.addEventListener('shown.bs.modal', (event) => {document.getElementById("cmnt-text").focus();});
		}
	});

})(Joomla.getOptions('Meedya'), jQuery);
