(function() {
	var
		vendorQuirk = {
			vendorID: 'w3',
			browserPrefix: '',
			reqFull: 'requestFullScreen',
			canFull: 'cancelFullScreen'
		};
	if (navigator.userAgent.indexOf('MSIE')!==-1 || navigator.appVersion.indexOf('Trident/') > 0) {
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
}());

// getElementById
function $id(id) {
	return document.getElementById(id);
}

var	ssCtl = (function() {
	var mySC = {autoPlay:true, slideDur:7000, trnType:"x", repeat:false},
		_items = null,		// array of items to display
		_ill = 0,			// itemslist length
		_iecnt = 5,			// max number of elements in the view frame
		_ffv = 2,			// frame focus view - generally the middle view of the frame
		_fle = 0,			// left edge of view frame relative to itemslist
		_ielms = [],		// elements associated with the view frame
		_vELm = null,		// reusable video element
		_fELm = null,		// current front image/video element
		_iarea = null,		// image area
		_iniClass,
		_onClass,
		_trzn,
		_slideDur = 7000,	// display duration for each image
		_sTimer = null,
		_running = false,
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

	var t_none = {
			preS: function (elm) {
				preSizeImage(elm, null);
			},
			pull: function (elm, lft) {
				elm.className = 'islide img_hide';
			},
			getI: function (elm, num, lft) {
				loadElm(elm, num, lft);
			},
			resz: function () {
				preSizeImages();
			}
		};
	var t_dzlv = {
			preS: function (elm) {
				preSizeImage(elm, null);
			},
			pull: function (elm, lft) {
				elm.className = 'islide img_off';
			},
			getI: function (elm, num, lft) {
				loadElm(elm, num, lft);
			},
			resz: function () {
				preSizeImages();
			}
		};
	var t_slid = {
			preS: function (elm, lft) {
				if (lft) {
					preSizeImage(elm, function(){ elm.style.left = -(elm.width+2)+"px"; });
				} else {
					preSizeImage(elm, function(){ elm.style.left = (window.innerWidth+2)+"px"; });
				}
			},
			pull: function (elm, lft) {
				if (lft) {
					//cause the currently displayed image to slide off screen to the left
					elm.style.left = -(elm.width+2)+"px";
				} else {
					//cause the currently displayed image to slide off screen to the right
					elm.style.left = (window.innerWidth+2)+"px";
				}
			},
			getI: function (elm, num, lft) {
				elm.className = "islide";
				loadElm(elm, num, lft);
			},
			resz: function () {	//adjust right side image positions after window resize
				var ehd = _iecnt - _ffv, i, elm;
				preSizeImages();
				// set right side elements
				for (i=0; i<ehd; i++) {
					elm = _ielms[i+_ffv];
					elm.style.left = (window.innerWidth+2)+"px";
				}
				//set left side elements
				for (i=0; i<_ffv; i++) {
					elm = _ielms[i];
					elm.style.left = -(elm.width+2)+"px";
				}
			}
		};

	function relSlidNum (reln) {
		if (reln < 0) {
			reln = (reln*-1) % _ill;
			return _ill - reln;
		} else {
			return reln % _ill;
		}
	}

	function loadElm (elm, lix, lft) {
		elm.eMsg = null;
		if (vendorQuirk.vendorID == "ff") { elm.src = ''; elm.completed = false; }	//for FF to full load image

		if (_items[lix].mTyp == "i") {
			elm.ism = false;
			elm.src = mySC.baseUrl + _items[lix].fpath;
		} else {
			elm.ism = true;
			elm.src = mySC._imgP+"blank.png";
		}

		elm.slidnum = lix;
		elm.isSized = false;
		trzn.preS(elm, lft);
	}

	function imgPlaced (elm) {
		elm.className = _onClass;
	//	_sldnumelm.innerHTML = elm.slidnum + 1;
		_titlelm.innerHTML = _items[elm.slidnum].title;
		if (elm.eMsg) { _titlelm.innerHTML += elm.eMsg; }
		if (_running && !_resizing) { _sTimer = setTimeout(function(){nextSlide()}, _slideDur); }
	}

	//rotate the img frame right (clockwise)
	function frameRight () {
		trzn.pull(_ielms[_ffv], true);
		var lf = _ielms.shift(),
			sNum = relSlidNum(_fle+_iecnt);
		trzn.getI(lf, sNum, false);
		_fle = (_fle+1) % _ill;
		_ielms.push(lf);
	}
	//rotate the img frame left (counterclockwise)
	function frameLeft () {
		trzn.pull(_ielms[_ffv], false);
		var rf = _ielms.pop(),
			sNum = relSlidNum(_fle-1);
		trzn.getI(rf, sNum, true);
		_fle = sNum;
		_ielms.unshift(rf);
	}
	function nextFrame (LR) {
		_vElm.pause();
		_vElm.style.display = "none";

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
//		_titlelm.innerHTML = _items[tElm.slidnum].title;
//		if (tElm.eMsg) { _titlelm.innerHTML += tElm.eMsg; }
if (LR !== 0) { _titlelm.innerHTML = ""; }
		if (tElm.ism) {
			// recover from prior swipe
			_vElm.style.left = "0px";
			_vElm.src = mySC.baseUrlV + _items[tElm.slidnum].fpath;
//			_sldnumelm.innerHTML = tElm.slidnum + 1;
			_fElm = _vElm;
		} else {
			// recover from prior swipe
			tElm.style.left = "0px";
			positionImage(tElm, function(){ imgPlaced(tElm); /*tElm.className = _onClass;*/});
			_fElm = tElm;
		}
//		_sldnumelm.innerHTML = tElm.slidnum + 1;
		return true;
	}
	function nextSlide () {
		nextFrame(1);
		//if (nextFrame(1) && _sTimer) _sTimer = setTimeout(function(){nextSlide()}, _slideDur);
	}
	function prevSlide () {
		nextFrame(-1);
	}

	function slideshowPause () {
		// stop the slideshow if it is automatically running.
		if (_sTimer) {
			clearTimeout(_sTimer);
			_sTimer = null;
		}
		_running = false;
	}

	function preSizeImages () {
		var i, icnt = _ielms.length;
		for (i=0; i<icnt; i++) {
			preSizeImage(_ielms[i], null);
		}
	}

	function preSizeImage (img, cb) {
		if (!img.complete) { setTimeout(function(){preSizeImage(img, cb)},100); return; }
		var bH = window.innerHeight/* - _titlelm.offsetTop*/,	//// - _titlelm.offsetHeight - 2,
			bW = _iarea.offsetWidth,
			pW = img.naturalWidth,
			pH = img.naturalHeight,
			fW, fH;
		if (_items[img.slidnum].title) { bH -= 26; }
		if (pW>0 && pH>0) {
			fH = pH>bH ? bH : pH;
			fW = Math.round(pW*fH/pH);
			if (fW>bW) {
				fW = bW;
				fH = Math.round(pH*fW/pW);
			}
//			img.height = pH;	//fH;
//			img.width = pW;		//fW;
			img.isSized = true;
		} else {
		}
		if (cb) cb();
	}

	function positionImage (img, cb) {
		if (!img.isSized) { _loading.style.display = "block"; setTimeout(function(){positionImage(img, cb)},100); return; }
		_loading.style.display = "none";
//		var bW = _iarea.offsetWidth,	//_iarea.innerWidth,
//			fW = img.width;
//			if (fW<bW) {
//				img.style.left = Math.floor((bW-fW)/2)+"px";
//			} else img.style.left = '0px';
		if (cb) cb();
	}

	function goToSlide (snum) {
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
	}

//===============================================================

	function rewindShow () {
		goToSlide(0);
		nextFrame(0);
	}

	function lastSlide () {
		goToSlide(_ill-1);
		nextFrame(0);
	}

	mySC.doMnu = function(cmd) {
		switch(cmd) {
			case _stop:
				_iarea.blur();
				$id('sstage').style.display = "none";
				_ielms.forEach(function(itm){_iarea.removeChild(itm);});
				_ielms = [];
				_iarea.removeChild(_vElm);
				break;
			case _rwnd:
				rewindShow();
				break;
			case _prev:
				prevSlide();
				break;
			case _next:
				nextSlide();
				break;
			case _last:
				lastSlide();
				break;
		}
	};

	function doKeyAction (e,code) {
		if (e.preventDefault) e.preventDefault();
		if (e.stopPropagation) e.stopPropagation();
		switch (code) {
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
		}
	}

	function keyPressed (e) {
		switch (e.charCode) {
			case 32:
			case 91:
			case 93:
			case 13:
				doKeyAction(e,e.charCode);
				break;
			default:
				//stelm = $id("status");
				//stelm.innerHTML += e.charCode+':';
				break;
		}
	}

	function keyDowned (e) {
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
				//stelm = $id("status");
				//stelm.innerHTML += e.keyCode+';';
				break;
		}
	}

	// see if there hase been a left or right swipe
	function swipe (e) {
		var te = e.changedTouches[0];
		var	dx = _tstartx - te.clientX,
			dy = _tstarty - te.clientY;
		if ((e.timeStamp - _tstartt) > 700) {
			_fElm.style.left = "0px";
			return;
		}
		if (Math.abs(dx) > Math.abs(dy)) {
			if (Math.abs(dx) > 100) {
				e.preventDefault();
				if (dx>0) {mySC.doMnu(_next)}
				else {mySC.doMnu(_prev)}
			}
		} else {
			if (Math.abs(dy) > 100) {
				e.preventDefault();
				mySC.doMnu(_paus);
			}
		}
	}
	// touch start
	function touch (e) {
		var ts = e.changedTouches[0];
		_tstartx = ts.clientX;
		_tstarty = ts.clientY;
		_tstartt = e.timeStamp;
	}
	// provide some swipe feedback
	function move (e) {
		var ts = e.changedTouches[0];
		var diff = ts.clientX - _tstartx;
		_fElm.style.left = diff+"px";
	}

	function winResized () {
		if (_resizeTime) clearTimeout(_resizeTime);
		_resizeTime=setTimeout(function(){_resizing=true;trzn.resz();nextFrame(0);_resizing=false}, 200);
	}

	function medEnded (elm) {
		if (_running) {
			nextSlide();
		}
	}

	function imgError () {
		this.eMsg = '<p class="errMsg">'+imgerror+this.src+'</p>';
		this.src = "components/com_meedya/static/css/broken.png";
	}

	function medError (evt) {
		_titlelm.innerHTML += '<p class="errMsg">'+viderror+this.src+'</p>';
		if (_running) { _sTimer = setTimeout(function(){nextSlide()}, _slideDur); }
	}

	mySC.sdur = function(up) {
		if (up) {_slideDur += 1000}
		else { if (_slideDur > 3000) {_slideDur -= 1000} }
	};

	mySC.init = function(items, stix) {
		var i, ielm;

		_items = items;

		_iarea = $id("iarea");

		trzn = t_none;	//use no transition by default
		_onClass = 'islide img_show';
		_iniClass = 'islide img_hide';

		switch (this.trnType) {
			case 'd':
				trzn = t_dzlv;
				_onClass = 'islide img_on';
				_iniClass = 'islide img_off';
				break;
			case 's':
				trzn = t_slid;	//use slide transition
				_onClass = 'islide img_slin';
				_iniClass = 'islide';
				break;
		}

		_ill = _items.length;
		if (_ill < _iecnt) { _iecnt = _ill; }
		for (i=0; i<_iecnt; i++) {
			ielm = document.createElement("IMG");
			ielm.onerror = imgError;
//			ielm.style.left = (window.innerWidth+2)+"px";
			ielm.className = _iniClass;
			_ielms.push(ielm);
			_iarea.appendChild(ielm);
		}

		_vElm = document.createElement("VIDEO");
		_vElm.style.display="none";
		_vElm.onerror = medError;
		_vElm.className = "medsld";
		_vElm.id = "medsld";
		_vElm.controls = true;
		_vElm.onended = function(){medEnded(this);};
		_vElm.onloadedmetadata = function(){this.style.display="block";this.play()};
		_iarea.appendChild(_vElm);

		// get the middle element of the image frame
		_ffv = Math.floor(_iecnt/2);

		// watch for swipes
		_iarea.addEventListener('touchstart', touch, false);
		_iarea.addEventListener('touchmove', move, false);
		_iarea.addEventListener('touchend', swipe, false);

		_iarea.addEventListener("keypress", keyPressed, false);
		_iarea.addEventListener("keydown", keyDowned, false);
		_iarea.focus();

		_sldnumelm = $id("slidnum");
		_titlelm = $id("ptext");
		_pauseRunDiv = $id("cb_paus");
		_loading = $id("loading");
		_slideDur = this.slideDur;
		goToSlide(stix);
		nextFrame(0);
//		window.onresize = winResized;
	};

	return mySC;
}());
