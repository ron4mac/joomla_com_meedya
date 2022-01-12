Meedya = {};	// a namespace for com_meedya

(function(my, $) {

	var token;

	var viewer = {
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
		showSlide: function (e, iid) {
			e.preventDefault();
			var imgl = JSON.parse(JSON.stringify(Meedya.items));	//copy
			if (Meedya.FB4) {
				Fancybox.show(imgl,{startIndex: iid});
			} else {
				$.fancybox.open(imgl, {...this.sopts, ...this.vopts, ...this.ivbuts}, iid);
			}
		},
		slideShow: function (e) {
			e.preventDefault();
			var imgl = JSON.parse(JSON.stringify(Meedya.items));	//copy
			$.fancybox.open(imgl, {...this.sopts, ...this.vopts, ...this.ssbuts});
		}
	};

	var old_viewer = {
		showSlide: function (e, iid) {
			e.preventDefault();
			$('#sstage').appendTo('body').show();
			ssCtl.init(Meedya.items, iid);
		}
	};

	Meedya.initIV = function (old=false) {
		Meedya.viewer = old ? old_viewer : viewer;
	};

	Meedya.performSearch = function (aform) {
		var sterm = $.trim(aform.sterm.value);
		if (sterm==='') {
			alert(this.L.no_sterm);
			return false;
		}
		aform.submit();
		return false;
	};

	Meedya.thmClick = function (e) {
		let clkd = e.target;
		let iid = 0;
		switch (clkd.className) {
			case 'imgthm':
				e.stopPropagation();
				viewer.showSlide(e, +clkd.parentElement.parentElement.parentElement.dataset.ix);
				break;
			case 'strating':
				clkd = clkd.parentElement;
			case 'strback':
				clkd = clkd.parentElement;
			case 'strate':
				e.stopPropagation();
				if (!rDlg) break;
				iid = +clkd.parentElement.parentElement.parentElement.parentElement.dataset.iid;
				Meedya.dorate(iid, clkd);
				break;
			case 'far fa-comments':
			case 'icon-comments-2':			// J3
				clkd = clkd.parentElement;
			case 'mycmnts':
			case 'mycmnts hasem':
				e.stopPropagation();
				iid = +clkd.parentElement.parentElement.parentElement.parentElement.dataset.iid;
				Meedya.doComments(iid, clkd);
				break;
		}
	}


	var rDlg, ssr, curRelm;

	function submitRating (evt) {
		if (evt.detail === 0) {
			if (!confirm("Clear rating for this item?")) return;
		}
		$.post(my.rawURL, {[token]: 1, task: 'rateItem', iid: curIid, val: evt.detail}, function (data) {
			curRelm.firstElementChild.firstElementChild.style.width = data+"%";
			$(rDlg).modal('hide');
			ssr.enable();
		})
		.fail(function(err) {
			alert(err.responseText);
			$(rDlg).modal('hide');
		});
	}

	// using fetch
	Meedya._dorate = function (itemid, relm) {
		curIid = itemid;
		curRelm = relm;
		let data = new FormData();
		data.append('task', 'rateChk');
		data.append('iid', itemid);
		fetch(my.rawURL, {method: 'POST', body: data})
		.then(resp => resp.text())
		.then(txt => txt ? alert(txt) : $(rDlg).modal('show'))
		.catch(err => console.log(err));
	}

	// using jquery
	Meedya.dorate = function (itemid, relm) {
		curIid = itemid;
		curRelm = relm;
		$.post(my.rawURL, {task: 'rateChk', iid: itemid})
		.done(function(){ $(rDlg).modal('show'); })
		.fail(function(err){ alert(err.responseText); });
	}


	var curIid = 0, curCelm;
	var cDlg, ncDlg, cElm;

	// using fetch
	function fetchComments (itemid) {
		curIid = itemid;
		cElm.innerHTML = '';
		let data = new FormData();
		data.append(`task`, 'getComments');
		data.append(`iid`, itemid);
		data.append(token, 1);
		let good = false;
		fetch(my.rawURL, {method: 'POST', body: data})
		.then(resp => { good = resp.ok; return resp.text(); })
		.then(txt => {if (good) {cElm.innerHTML = txt; $(cDlg).modal('show');} else alert(txt)})
		.catch(err => console.log(err));
	};

	// using jquery
	function _fetchComments (itemid) {
		curIid = itemid;
		cElm.innerHTML = '';
		$.post(my.rawURL, {[token]: 1, task: 'getComments', iid: itemid})
		.done(function(data){ cElm.innerHTML = data; $(cDlg).modal('show'); })
		.fail(function(err){ alert(err.responseText); });
	};

	Meedya._fetchComments = async function (itemid) {
		let url = my.rawURL;
		let data = new URLSearchParams();
		data.append(`task`, 'getComments');
		data.append(`iid`, itemid);
		data.append(token, 1);
		const options = {
			method: 'POST',
			body: data
		}
		let response = await fetch(url, options);
//		if (!response.ok) {
//			throw new Error (Joomla.Text._('COM_MYCOMPONENT_JS_ERROR_STATUS') + `${response.status}`);
///		} else {
			let result = await response.text();	//console.log(result);
			cElm.innerHTML = result;
//		}
	};

	Meedya.submitComment = async function (elm) {
		elm.disabled = true;
		let fData = new FormData(newcmnt);
		fData.append(token, 1);
		fData.append('iid', curIid);
		const options = {
			method: 'POST',
			body: fData
		}
		let response = await fetch(my.rawURL, options);
		let result = await response.text();
		$(ncDlg).modal('hide');
		elm.disabled = false;
		curCelm.classList.add('hasem');
		curCelm.innerHTML = result;
	}

	Meedya.doComments = function (itemid, elm) {
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
	Meedya.sprintf = function (format) {
		for (var i = 1; i < arguments.length; i++) {
			format = format.replace( /%s/, arguments[i] );
		}
		return format;
	};

	function _t (tid) {
		return Meedya.L[tid] ? Meedya.L[tid] : tid;
	}

	$(document).ready(function() {
		$('['+Meedya.datatog+'="tooltip"]').tooltip();
		// get the joomla suplied csrf token
		token = Joomla.getOptions('csrf.token', '');
		// setup the star rating modal
		rDlg = document.getElementById('rating-modal');
		if (rDlg) {
			rDlg.addEventListener('hidden.bs.modal', function (event) {$(rDlg).modal('hide');});		// NOT SURE ABOUT THIS
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
			ncElm = document.querySelector(".modal-body textarea");
			// focus on the textarea
			ncDlg.addEventListener('shown.bs.modal', function (event) {document.getElementById("cmnt-text").focus();});
		}
	});

})(Joomla.getOptions('Meedya'), jQuery);
