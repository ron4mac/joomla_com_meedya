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
			buttons: ["zoom","slideShow","fullScreen","close"]
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
	});

})(jQuery);
