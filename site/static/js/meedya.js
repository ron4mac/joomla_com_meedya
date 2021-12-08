Meedya = {};	// a namespace for com_meedya

(function($) {

	//var itemslist = [];

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
			$.fancybox.open(imgl, {...this.sopts, ...this.vopts, ...this.ivbuts}, iid);
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
			jQuery('#sstage').appendTo('body').show();
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

	Meedya.dorate = function (itemid, relm) {
		jQuery.post(Meedya.rawURL, {[Meedya.formTokn]: 1, task: 'rateChk', iid: itemid}, function (data) {
		})
		.done(function(){
			var rDlg = document.getElementById('rating-modal');
			var rModal = new bootstrap.Modal(rDlg);
			rDlg.addEventListener('hidden.bs.modal', function (event) {rModal.hide();});
			rModal.show();
	//		bootbox.dialog({
	//			title: _t('rate_item'),
			//	message: '<div class="rated"><span id="unrating" class="rating" data-default-rating="2.5"></span></div>'
	//			message: '<div class="rated"><span id="bunrating" class="rating"></span></div>'
	//		});
			var rating = document.getElementById('unrating');
			rating.innerHTML = '';
			var r = new SimpleStarRating(rating);
			rating.addEventListener('rate', function(e) {
				if (e.detail === 0) {
					if (!confirm("Clear rating for this item?")) return;
				}
				jQuery.post(Meedya.rawURL, {[Meedya.formTokn]: 1, task: 'rateItem', iid: itemid, val: e.detail}, function (data) {
				//	alert(data);
					relm.firstElementChild.firstElementChild.style.width = data+"%";
					bootbox.hideAll();
					rModal.hide();	//.dispose();
				})
				.fail(function(err) {
					alert(err.responseText);
					bootbox.hideAll();
					rModal.hide();	//.dispose();
				});
			});
		})
		.fail(function(err){
			alert(err.responseText);
		//	bootbox.alert({
		//		message: err.responseText
		//	});
		});
	}

	var curIid = 0, curCelm;
	var cDlg, cModal, cElm;
	Meedya.doComments = function (itemid, elm) {
		document.getElementById("cmnt-text").value = "";
		curIid = itemid;
		curCelm = elm;
		if (elm.classList.contains('hasem')) {
			Meedya.fetchComments(itemid);
			cModal.show();
		} else {
			ncModal.show();
		}
	};

	Meedya.fetchComments = function (itemid) {
		curIid = itemid;
		cElm.innerHTML = '';
		const token = Joomla.getOptions('csrf.token', '');
		let url = Meedya.rawURL;
		let data = new URLSearchParams();
		data.append(`task`, 'getComments');
		data.append(`iid`, itemid);
		data.append(token, 1);
		const options = {
			method: 'POST',
			body: data,
			cache: "no-cache",
			headers: new Headers({cache:"no-store"})
		}
		fetch(url, options)
		.then(resp => resp.text())
		.then(function(rresp) {
			if (!rresp.ok) {
//				throw new Error (Joomla.Text._('COM_MYCOMPONENT_JS_ERROR_STATUS') + `${resp.status}`);
//			} else {
				console.log(rresp);
				let result = rresp;	//console.log(result);
				cElm.innerHTML = result;
			}
//			console.log(resp);
			})
		.catch(function(error) {
			console.log(error);
		});

//		if (!response.ok) {
//			throw new Error (Joomla.Text._('COM_MYCOMPONENT_JS_ERROR_STATUS') + `${response.status}`);
//		} else {
//			let result = await response.text();	console.log(result);
//			let description = document.querySelector(".modal-body .comments");
//			description.innerHTML = result;
//		}
	};

	Meedya._fetchComments = async function (itemid) {
		const token = Joomla.getOptions('csrf.token', '');
		let url = Meedya.rawURL;
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
		let url = Meedya.rawURL;
		let fData = new FormData(newcmnt);
		fData.append(Meedya.formTokn, 1);
		fData.append('iid', curIid);
		fData.append('uid', 260);
		const options = {
			method: 'POST',
			body: fData
		}
		let response = await fetch(url, options);
		let result = await response.text();
		ncModal.hide();
		elm.disabled = false;
		curCelm.classList.add('hasem');
		curCelm.innerHTML = result;
	}

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
		$('[data-toggle="tooltip"]').tooltip();
		cDlg = document.getElementById('comments-modal');
		cElm = document.querySelector(".modal-body .comments");
		cModal = new bootstrap.Modal(cDlg);
		ncDlg = document.getElementById('comment-modal');
		ncElm = document.querySelector(".modal-body textarea");
		ncModal = new bootstrap.Modal(ncDlg);
		ncDlg.addEventListener('shown.bs.modal', function (event) {document.getElementById("cmnt-text").focus();});
	});

})(jQuery);
