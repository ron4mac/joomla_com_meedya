/* globals vendorQuirk, fullScreenApi */
"use strict";

(function() {
	let vendorQuirk = {
			vendorID: 'w3',
			browserPrefix: '',
			reqFull: 'requestFullScreen',
			canFull: 'cancelFullScreen'
		};
	if (navigator.appName == 'Microsoft Internet Explorer') {
		vendorQuirk.vendorID = 'ie';
		vendorQuirk.browserPrefix = 'ms';
	} else if (navigator.userAgent.indexOf('WebKit') > -1) {
		vendorQuirk.vendorID = 'wk';
		vendorQuirk.browserPrefix = 'webkit';
	} else if (navigator.userAgent.indexOf('Gecko') > -1) {
		vendorQuirk.vendorID = 'ff';
		vendorQuirk.browserPrefix = 'moz';
	} else if (navigator.userAgent.indexOf('Opera') > -1) {
		vendorQuirk.vendorID = 'op';
		vendorQuirk.browserPrefix = 'o';
	} else if (navigator.userAgent.indexOf('KHTML') > -1) {
		vendorQuirk.vendorID = 'kd';
		vendorQuirk.browserPrefix = 'khtml';
	}
	// export quirks
	window.vendorQuirk = vendorQuirk;
})();


(function() {
	let fullScreenApi = {
			supportsFullScreen: false,
			isFullScreen: () => false,
			requestFullScreen: () => {},
			cancelFullScreen: () => {},
			fullScreenEventName: '',
			prefix: ''
		};

	// check for native support
	if (typeof document.cancelFullScreen != 'undefined') {
		fullScreenApi.supportsFullScreen = true;
	} else {
		// check for fullscreen support by vendor prefix
		if (typeof document[vendorQuirk.browserPrefix + 'CancelFullScreen'] != 'undefined') {
			fullScreenApi.supportsFullScreen = true;
			fullScreenApi.prefix = vendorQuirk.browserPrefix;
		}
	}

	// update methods to do something useful
	if (fullScreenApi.supportsFullScreen) {
		fullScreenApi.fullScreenEventName = fullScreenApi.prefix + 'fullscreenchange';
		fullScreenApi.isFullScreen = () => {
			switch (fullScreenApi.prefix) {
				case '':
					return document.fullScreen;
				case 'webkit':
					return document.webkitIsFullScreen;
				default:
					return document[fullScreenApi.prefix + 'FullScreen'];
			}
		};
		fullScreenApi.requestFullScreen = (el) => {
			return (fullScreenApi.prefix === '') ? el.requestFullScreen() : el[fullScreenApi.prefix + 'RequestFullScreen']();
		};
		fullScreenApi.cancelFullScreen = () => {
			return (fullScreenApi.prefix === '') ? document.cancelFullScreen() : document[fullScreenApi.prefix + 'CancelFullScreen']();
		};
	}
	// export api
	window.fullScreenApi = fullScreenApi;
})();


var	ssCtl = (function() {
	let mySC = {autoPlay:true, slideDur:7000, trnType:'d', repeat:false},
		_ill = 0,			// imagelist length
		_iecnt = 5,			// max number of elements in the view frame
		_ffv = 2,			// frame focus view - generally the middle view of the frame
		_fle = 0,			// left edge of view frame relative to imagelist
		_ielms = [],		// elements associated with the view frame
		_vElm = null,		// reusable video element
		_iniClass,
		_onClass,
		_trzn,
		_winwid = window.innerWidth + 2,
		_slideDur = 7000,	// display duration for eash image
		_sTimer = null,
		_running = false,
		_fullScreenApi = null,
		_topMargin = 22,
		_sldnumelm = null,
		_titlelm = null,
		_pauseRunDiv = null,
		_loading,
		_resizeTime = null,
		_resizing = false,
		_tstartx,
		_tstarty,
		_tstartt,
		_stop = 0,
		_rwnd = 1,
		_prev = 2,
		_paus = 3,
		_next = 4,
		_last = 5,
		_fuls = 6;

	// getElementById
	/** @noinline */
	const _id = (id) => document.getElementById(id);

	// addEventListener
	const _ae = (elm, evnt, func, capt=false) => {
		if (typeof elm === 'string') elm = _id(elm);
		elm.addEventListener(evnt, func, capt);
	};

	// set the source url for the specified display element
	const loadElm = (frm, lix, lft) => {
		let elm = frm.firstChild;
		elm.eMsg = null;
		if (vendorQuirk.vendorID == 'ff') { elm.src = ''; elm.completed = false; }	//for FF to full load image

		if (imagelist[lix].mTyp == 'i') {
			frm.ism = false;
			elm.src = baseUrl + imagelist[lix].fpath;
		} else {
			frm.ism = true;
			elm.src = _imgP+'1x1.png';
		}

		frm.slidnum = elm.slidnum = lix;
		elm.isSized = false;
	//	_trzn.preS(elm, lft);
		_trzn.preS(frm, lft);
	};

	const preSizeImage = (frm, cb) => {
		let img = frm.firstChild;
		if (!img.complete) { setTimeout(() => preSizeImage(frm, cb), 100); return; }
		let bH = window.innerHeight - _titlelm.offsetTop - 2,	//// - _titlelm.offsetHeight - 2,
			bW = window.innerWidth,
			pW = img.naturalWidth,
			pH = img.naturalHeight,
			fW, fH;
//		if (imagelist[img.slidnum].title) { bH -= 26; }
		if (pW>0 && pH>0) {
			fH = pH>bH ? bH : pH;
			fW = Math.round(pW*fH/pH);
			if (fW>bW) {
				fW = bW;
				fH = Math.round(pH*fW/pW);
			}
			img.height = fH;
			img.width = fW;
			img.isSized = true;
		} else {
		}
		if (cb) cb();
	};

	const preSizeImages = () => {
		let i, icnt = _ielms.length;
		for (i=0; i<icnt; i++) {
			preSizeImage(_ielms[i], null);
		}
	};

	var t_none = {
			preS: (elm) => preSizeImage(elm, null)
			,
			pull: (elm, lft) => { elm.className = 'islide img_hide' }
			,
			getI: (elm, num, lft) => loadElm(elm, num, lft)
			,
			resz: () => preSizeImages()
		};
	var t_dzlv = {
			preS: (elm) => preSizeImage(elm, null)
			,
			pull: (elm, lft) => { elm.className = 'islide img_off'; }
			,
			getI: (elm, num, lft) => loadElm(elm, num, lft)
			,
			resz: () => preSizeImages()
		};
	var t_slid = {
			preS: (elm, lft) => {
				if (lft) {
					preSizeImage(elm, () => { elm.style.left = -_winwid+'px'; });
				} else {
					preSizeImage(elm, () => { elm.style.left = _winwid+'px'; });
				}
			},
			pull: (elm, lft) => {
				if (lft) {
					//cause the currently displayed image to slide off screen to the left
					elm.style.left = -_winwid+'px';		//-(elm.width+2)+'px';
				} else {
					//cause the currently displayed image to slide off screen to the right
					elm.style.left = _winwid+'px';
				}
			},
			getI: (elm, num, lft) => {
				elm.className = 'islide';
				loadElm(elm, num, lft);
			},
			resz: () => {	//adjust right side image positions after window resize
				var ehd = _iecnt - _ffv, i, elm;
				preSizeImages();
				// set right side elements
				for (i=0; i<ehd; i++) {
					elm = _ielms[i+_ffv];
					elm.style.left = _winwid+'px';
				}
				//set left side elements
				for (i=0; i<_ffv; i++) {
					elm = _ielms[i];
					elm.style.left = -_winwid+'px';
				}
			}
		};

	const relSlidNum = (reln) => {
		if (reln < 0) {
			reln = (reln*-1) % _ill;
			return _ill - reln;
		} else {
			return reln % _ill;
		}
	};

	const imgPlaced = (elm) => {
		elm.className = _onClass;
		_sldnumelm.innerHTML = elm.slidnum + 1;
		_titlelm.innerHTML = imagelist[elm.slidnum].title;
		if (elm.eMsg) { _titlelm.innerHTML += elm.eMsg; }
		if (_running && !_resizing) { _sTimer = setTimeout(() => nextSlide(), _slideDur); }
	};

	//rotate the img frame right (clockwise)
	const frameRight = () => {
		_trzn.pull(_ielms[_ffv], true);
		var lf = _ielms.shift(),
			sNum = relSlidNum(_fle+_iecnt);
		_trzn.getI(lf, sNum, false);
		_fle = (_fle+1) % _ill;
		_ielms.push(lf);
	};

	//rotate the img frame left (counterclockwise)
	const frameLeft = () => {
		_trzn.pull(_ielms[_ffv], false);
		var rf = _ielms.pop(),
			sNum = relSlidNum(_fle-1);
		_trzn.getI(rf, sNum, true);
		_fle = sNum;
		_ielms.unshift(rf);
	};

	const positionImage = (frm, cb) => {
		let img = frm.firstChild;
		if (!img.isSized) { _loading.style.display = 'block'; setTimeout(() => positionImage(frm, cb), 100); return; }
		_loading.style.display = 'none';
		var bW = window.innerWidth,
			fW = img.width;
		//	if (fW<bW) {
		//		img.style.left = Math.floor((bW-fW)/2)+'px';
		//	} else img.style.left = '0px';
		frm.style.left = '0';
		if (cb) cb();
	};


	const nextFrame = (LR) => {
		_vElm.pause();
		_vElm.style.display = 'none';
		
		if (!mySC.repeat) {
			if ((LR==1 && _fle+_ffv+1==_ill)) {
				mySC.doMnu(_stop);
				return false;
			}
		}
		if (_ielms.length > 1) {
			if (LR>0) { frameRight(); } else if (LR<0) { frameLeft(); }
		}
		var tElm = _ielms[_ffv];
//		_titlelm.innerHTML = imagelist[tElm.slidnum].title;
//		if (tElm.eMsg) { _titlelm.innerHTML += tElm.eMsg; }
if (LR !== 0) { _titlelm.innerHTML = ''; }
		if (tElm.ism) {
			_vElm.src = baseUrl + imagelist[tElm.slidnum].fpath;
			_sldnumelm.innerHTML = tElm.slidnum + 1;
		} else {
			positionImage(tElm, () => { imgPlaced(tElm); /*tElm.className = _onClass;*/});
		}
//		_sldnumelm.innerHTML = tElm.slidnum + 1;
		return true;
	};

	const nextSlide = () => {
		nextFrame(1);
		//if (nextFrame(1) && _sTimer) _sTimer = setTimeout(function(){nextSlide()}, _slideDur);
	};
	const prevSlide = () => {
		nextFrame(-1);
	};

	const slideshowPause = () => {
		// stop the slideshow if it is automatically running.
		if (_sTimer) {
			clearTimeout(_sTimer);
			_sTimer = null;
		}
		_running = false;
	};

	const slideshowRun = () => {
		_pauseRunDiv.style.backgroundPosition = '-54px 0';
		slideshowPause();
//		positionImage(_ielms[_ffv], null);
//		_sTimer = setTimeout(function(){nextSlide()}, _slideDur);
		_running = true;
		nextFrame(0);
	};

	const slideshowStop = () => {
		_pauseRunDiv.style.backgroundPosition = '-72px 0';
		slideshowPause();
	};

	const goToSlide = (snum) => {
		var ehd, i, rn;
		// set the frame left edge
		_fle = relSlidNum(snum - _ffv);
		// get right side count
		ehd = _iecnt - _ffv;
		// set right side elements
		for (i=0; i<ehd; i++) {
			rn = relSlidNum(i+snum);
			loadElm(_ielms[i+_ffv], rn, false);
		}
		//set left side elements
		for (i=0; i<_ffv; i++) {
			rn = relSlidNum(snum - _ffv + i);
			loadElm(_ielms[i], rn, true);
		}
	};

//===============================================================

	const runToggle = () => {
		if (_sTimer) {
			slideshowStop();
		} else {
			slideshowRun();
		}
	};

	const rewindShow = () => {
		slideshowStop();
		goToSlide(0);
		nextFrame(0);
	};

	const lastSlide = () => {
		slideshowStop();
		goToSlide(_ill-1);
		nextFrame(0);
	};

	const toggleFully = () => {
		var tfsdiv = _id('cb_full');
		if (_fullScreenApi.isFullScreen()) {
			_fullScreenApi.cancelFullScreen();
			tfsdiv.style.backgroundPosition = '-126px 0';
		}
		else {
			if (_fullScreenApi.supportsFullScreen) {
				_fullScreenApi.requestFullScreen(document.body);
				tfsdiv.style.backgroundPosition = '-144px 0';
			}
		}
	};

	mySC.doMnu = (cmd) => {
		switch(cmd) {
			case _stop:
				if (popdwin) { window.close(); }
				else { window.history.back(); }
				break;
			case _rwnd:
				rewindShow();
				break;
			case _prev:
				slideshowStop();
				prevSlide();
				break;
			case _paus:
				runToggle();
				break;
			case _next:
				slideshowStop();
				nextSlide();
				break;
			case _last:
				lastSlide();
				break;
			case _fuls:
				toggleFully();
				break;
		}
	};

	const doKeyAction = (e, code) => {
		if (e.preventDefault) e.preventDefault();
		if (e.stopPropagation) e.stopPropagation();
		switch (code) {
			case 32:
				mySC.doMnu(_paus);
				break;
			case 37:
				mySC.doMnu(_prev);
				break;
			case 39:
				mySC.doMnu(_next);
				break;
			case 27:
				mySC.doMnu(_stop);
				break;
			case 91:
				mySC.doMnu(_rwnd);
				break;
			case 93:
				mySC.doMnu(_last);
				break;
			case 9:
			case 13:
				mySC.doMnu(_fuls);
				break;
		}
	};

	const keyPressed = (e) => {
		switch (e.charCode) {
			case 32:
			case 91:
			case 93:
			case 13:
				doKeyAction(e,e.charCode);
				break;
			default:
				//stelm = _id("status");
				//stelm.innerHTML += e.charCode+':';
				break;
		}
	};

	const keyDowned = (e) => {
		switch (e.keyCode) {
			case 32:
			case 13:
				break;
			case 37:
			case 39:
			case 27:
			case 9:
				doKeyAction(e,e.keyCode);
				break;
			default:
				//stelm = _id("status");
				//stelm.innerHTML += e.keyCode+';';
				break;
		}
	};

	const swipe = (e) => {
		var te = e.changedTouches[0];
		var	dx = _tstartx - te.clientX,
			dy = _tstarty - te.clientY;
		e.preventDefault();
		if ((e.timeStamp - _tstartt) > 400) { return; }
		if (Math.abs(dx) > Math.abs(dy)) {
			if (Math.abs(dx) > 150) {
				if (dx>0) {mySC.doMnu(_next)}
				else {mySC.doMnu(_prev)}
			}
		} else {
			if (Math.abs(dy) > 150) {
				mySC.doMnu(_paus);
			}
		}
	};
	const touch = (e) => {
		var ts = e.changedTouches[0];
		_tstartx = ts.clientX;
		_tstarty = ts.clientY;
		_tstartt = e.timeStamp;
		e.preventDefault();
	};

	const winResized = () => {
		if (_resizeTime) clearTimeout(_resizeTime);
		_resizeTime = setTimeout(() => {
			_winwid = window.innerWidth + 2;
			_resizing = true;
			_trzn.resz();
			nextFrame(0);
			_resizing = false}
			, 200);
	};

	const showSeconds = () => {
		var sspan = _id('seconds');
		sspan.innerHTML = Math.round(_slideDur/1000);
	};

	const medEnded = (e) => {
		if (_running) {
			setTimeout(() => nextSlide(), 1000);
		}
	};

	const imgError = (e) => {
		let t = e.target;
		t.eMsg = '<p class="errMsg">'+imgerror+t.src+'</p>';
		t.src = 'components/com_meedya/static/css/broken.png';
	};

	const medError = (e) => {
		let t = e.target;
		_titlelm.innerHTML += '<p class="errMsg">'+viderror+t.currentSrc+'</p>';
		if (_running) { _sTimer = setTimeout(() => nextSlide(), _slideDur); }
	};

	mySC.sdur = (up) => {
		if (up) {_slideDur += 1000}
		else { if (_slideDur > 3000) {_slideDur -= 1000} }
		showSeconds();
	};

	mySC.init = (fsapi) => {
		var i, felm, ielm, iarea = _id('screen');

		_trzn = t_none;	//use no transition by default
		_onClass = 'islide img_show';
		_iniClass = 'islide img_hide';

		switch (mySC.trnType) {
			case 'd':
				_trzn = t_dzlv;
				_onClass = 'islide img_on';
				_iniClass = 'islide img_off';
				break;
			case 's':
				_trzn = t_slid;	//use slide transition
				_onClass = 'islide img_slin';
				_iniClass = 'islide';
				break;
		}

		_ill = imagelist.length;
		if (_ill < _iecnt) { _iecnt = _ill; }
		for (i=0; i<_iecnt; i++) {
			felm = document.createElement('DIV');
			felm.style.left = _winwid+'px';
			felm.className = _iniClass;

			ielm = document.createElement('IMG');
			ielm.onerror = imgError;

			felm.appendChild(ielm);
			_ielms.push(felm);

			iarea.appendChild(felm);
		}

		_vElm = document.createElement('VIDEO');
		_vElm.style.display = 'none';
		_vElm.onerror = medError;
		_vElm.className = 'medsld';
		_vElm.id = 'medsld';
		_vElm.controls = true;
		_vElm.onended = medEnded;
	//	_vElm.autoplay=true;
	//	_vElm.muted=true;
		_vElm.playsinline=true;
		_vElm.onloadedmetadata = (e) => {let t=e.target;t.style.display='block';t.play();};
		iarea.appendChild(_vElm);

		// get the middle element of the image frame
		_ffv = Math.floor(_iecnt/2);

		// watch for swipes
		_ae(iarea, 'touchstart', touch);
		_ae(iarea, 'touchmove', (e) => e.preventDefault());
		_ae(iarea, 'touchend', swipe);

		_fullScreenApi = fsapi;
		_sldnumelm = _id('slidnum');
		_titlelm = _id('ptext');
		_pauseRunDiv = _id('cb_paus');
		_loading = _id('loading');
		_slideDur = mySC.slideDur;
		showSeconds();
		goToSlide(0);
		if (mySC.autoPlay) {
			slideshowRun();
		} else {
			nextFrame(0);
		}
	//	window.onresize = winResized;
		_ae(window, 'resize', winResized);
	};

	_ae(document, 'keypress', keyPressed);
	_ae(document, 'keydown', keyDowned);

	return mySC;
})();

window.onload = function(){ssCtl.init(fullScreenApi);};
