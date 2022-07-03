/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
/* globals Joomla */
/* uplodr v0.9 */
'use strict';

/* encapsulate the entire upload engine in a function */
(function (w, CFG) {

	// a couple of utility functions to assist in minification
	/** @noinline */
	const _id = (id) => document.getElementById(id);
	/** @noinline */
	const _ae = (elem, evnt, func) => elem.addEventListener(evnt, func, false);

	const defaults = {
			lodrdiv: 'uplodr',
			upURL: 'upload.php',
			payload: () => {return {}},	// get other data to be sent along with the file data
			maxFilesize: 134217728,
			dropMessage: 'Drop files here to upload<br>(or click to select)',
			failcss: 'failure',
			concurrent: 3,
			maxchunksize: 16777216,		// 16M
			allowed_file_types: [],	// all
			success: (resp) => {},
			doneFunc: (good,bad) => {}
		};

	let opts = Object.assign({}, defaults, CFG),
		totProgressBar,
		progressDiv,
		qCountSpan,
		fdonetarget,
		upQueue = [],
		maxXfer = opts.concurrent,
		aft = opts.allowed_file_types,
		maxcnksz = opts.maxchunksize,
		qStopt = false,
		inPrg = 0,
		total2do = 0,
		totalDone = 0,
		allDone = 0,
		okCount = 0,
		errCount = 0,
		e_st, e_rs, e_cn,
		slfunc = '',
		_qCtrl = {
			stop: function () {
				qStopt = true;
				e_st.disabled = true;
				e_rs.disabled = false;
				e_cn.disabled = false;
				},
			go: function () {
				qStopt = false;
				e_st.disabled = false;
				e_rs.disabled = true;
				e_cn.disabled = false;
				while (upQueue.length && (inPrg < maxXfer)) NextInQueue(false,'go');
				},
			cancel: function () {
				upQueue.length = 0;
				qStopt = false;
				e_st.disabled = true;
				e_rs.disabled = true;
				e_cn.disabled = true;
				qCountSpan.innerHTML = 0;
				if (!inPrg) _endUp();
				}
			}
		;

	// utility element creator
	let CreateElement = (type, cont, attr) => {
		let elem = document.createElement(type);
		if (cont) elem.innerHTML = cont;
		for (let key in attr) {
			elem.setAttribute(key, attr[key]);
		}
		return elem;
	};

	// file drag hover
	let FileDragHover = (e) => {
		e.stopPropagation();
		e.preventDefault();
		e.target.className = (e.type == 'dragover' ? 'hover' : 'dropzone');
	};

	// file selection
	let FileSelectHandler = (e) => {
		let files, i, f;

		if (e instanceof FileList) {
			files = e;
		} else {
			// cancel event and hover styling
			FileDragHover(e);

			// fetch FileList object
			files = e.target.files || e.dataTransfer.files;
		}

		if (allDone) allDone = total2do = totalDone = 0;

		// process all File objects
		for (i = 0; (f = files[i]); i++) {
			total2do += f.size;
			upQueue.push(f);
			qCountSpan.innerHTML = upQueue.length;
			NextInQueue(false,'fsel');
		}
		if (upQueue.length > maxXfer) e_st.disabled = false;
	};

	let _endUp = () => {	//console.log("ENDUP");
		if (!qStopt || !upQueue.length) {
			allDone = 1;
			if (typeof(opts.doneFunc) == 'function') {
				let okC = okCount, errC = errCount;
				setTimeout(() => opts.doneFunc(okC, errC), 1000);
			}
			errCount = okCount = 0;
		}
	};

	let NextInQueue = (decr,tag) => {
		if (decr) {
			if (tag == 'ufo') okCount++;
			if (! --inPrg) { _endUp(); }
		}
		if (!qStopt && upQueue.length && (!maxXfer || inPrg < maxXfer)) {
			let nxf = upQueue.shift();
			let ufo = new UpldFileObj(nxf);			//console.log(ufo);
			inPrg++;
			qCountSpan.innerHTML = upQueue.length;
		}
		if (upQueue.length <= 0) {
			e_st.disabled = true;
			e_rs.disabled = true;
			e_cn.disabled = true;
		}
	};

	// progress bar object
	function ProgressBar (fileObj, sclass) {
		let $ = this;
		
		$.show = (percent) => {
			let p = 100 * percent;
			$.pb.style.width = p + "%";
			if (percent === 1) {
				$.pb.className = 'indeterm';
			}
		};
		$.msg = (msg, err) => {
			$.pbi.innerHTML += '<br />' + msg;
			if (err) {
				$.pbi.className = 'pbfinf '+opts.failcss;
				errCount++;
			}
		};
		$.rmov = () => {
			$.pbw._ufo = null;
			progressDiv.removeChild($.pbw);
			$.fObj = null;
		};

		// create progress bar
		let pbw = CreateElement('div', '', {class:'pbwrp'});
		$.pb = pbw.appendChild(CreateElement('div', '', {class:sclass}));
		let pbv = fileObj.fn + '<i class="fa fa-window-close abortX" aria-hidden="true" onclick="this.parentNode.parentNode._ufo.abort(true);"></i>';
		$.pbi = pbw.appendChild(CreateElement('div', pbv, {class:'pbfinf'}));
		progressDiv.appendChild(pbw);
		$.pbw = pbw;
		$.pbw._ufo = fileObj;
		$.fObj = fileObj;
		return $;
	}

	let UpdateTotalProgress = (adsz) => {
		if (!totProgressBar) return;
		if (adsz < 0) return;
		totalDone += adsz;
		let wp = 100 * totalDone / total2do;
		totProgressBar.style.width = wp + '%';
	};

	let addData = (frmd, data) => {
		for (let key in data) {
			frmd.set(key, data[key]);
		}
	};

	// object for a file upload with chunking support
	function UpldFileObj (file) {
		let $ = this;
		$.upFile = file;
		$.fn = file.fileName || file.name;
		$.size = file.fileSize || file.size;	//console.log('FSize '+$.size);
		$.fType = file.fileType || file.type;
		$.date = file.lastModified;
		$.upState = '';
		$.doChnk = ($.size > maxcnksz);
		$.chnkSize = maxcnksz;		//Math.round(maxcnksz / 2);
		$.relPath = file.webkitRelativePath || $.fn;
		$.uniqueId = $.size + '-' + Math.random().toString().replace('0.', '');
		$.actSize = $.startByte = $.lastsz = $.chnkNum = 0;
		$.numChnks = Math.ceil($.size / $.chnkSize);		//Math.max(Math.floor($.size / $.chnkSize), 1);
		$.fData = $.pBar = null;
		$.palod = opts.payload();
		$.upForm = {task: 'manage.upfile', [Joomla.getOptions('csrf.token')]: 1};	// extra data can be added
		$.X = new XMLHttpRequest();

		const endup = (all) => {
			if ($.X) {
				if (all) {
					let tpce = new CustomEvent('mdya.totp',{detail: $.X.response});
					fdonetarget.dispatchEvent(tpce);
				}
				$.X.upload.onprogress = null;
				$.X.onabort = null;
				$.X.onerror = null;
				$.X.onload = null;
				$.X = null;
			}
			$.fData = null;
			if (all && $.pBar) {
				$.pBar.rmov();
				$.pBar = null;
			}
			NextInQueue(true,'ufo');
		};

		const fDat = (incpl=true) => {
			$.fData = new FormData();
			addData($.fData, $.upForm);
			if (incpl) addData($.fData, $.palod);
		};

		const state = () => {
			fDat();
			switch ($.upState) {
				case '':
					addData($.fData, {type: $.fType, size: $.size, lastMod: $.date});
					$.fData.set('Filedata', $.upFile);
					$.upState = 'upld';
					break;
				case 'upld':
					endup(true);
					return;
			}
			$.X.open('POST', opts.upURL);
			$.X.send($.fData);
		};

		const cstate = () => {
			fDat(false);
			switch ($.upState) {
				case '':
					//console.log('pref',$.fn);
					addData($.fData, { chunkact: 'pref', file: $.fn, type: $.fType, size: $.size, ident: $.uniqueId});
					$.upState = 'chnk';
					break;
				case 'chnk':
					//console.log('chnk',$.chnkNum+1,$.fn);
					addData($.fData, { chunkact: 'chnk', ident: $.uniqueId, /*fname: $.fn,*/ tchnk: $.numChnks });
					if ($.chnkNum == $.numChnks) { endup(true); return; }	///////// do stuff here to finish up
					$.startByte = $.chnkNum * $.chnkSize;
					let endByte = Math.min($.size, $.startByte + $.chnkSize);
				//	if ($.size - endByte < $.chnkSize) {
						// The last chunk will be bigger than the chunk size, but less than 2*chunkSize
				//		endByte = $.size;
				//	}
					$.actSize = endByte - $.startByte;		//console.log('CHK '+$.chnkNum+':'+$.actSize);
					$.fData.set('chnkn', ++$.chnkNum);
					if ($.chnkNum == $.numChnks) {
						addData($.fData, { fname: $.fn, type: $.fType, size: $.size, lastMod: $.date });
						addData($.fData, $.palod);
					}
					//console.log([...$.fData.entries()]);
					$.fData.set('Filedata', $.upFile[slfunc]($.startByte, endByte));
					$.lastsz = 0;
					break;
				case 'abrt':
					//console.log('abrt',$.fn);
					$.X.timeout = 10000;
					addData($.fData, { chunkact: 'abrt', ident: $.uniqueId });
					$.upState = 'nil';
					break;
				case 'nil':
					//console.log('nil ',$.fn);
					endup();
					return;
			}
			//console.log($.chnkNum, $.fn);
			$.X.open('POST', opts.upURL);
			$.X.send($.fData);
		};

		const errOut = (eMsg) => {
			$.pBar.msg(eMsg, true);
			$.X = null;
			NextInQueue(true,'errM');
		};

		const cb = {
			//upload progress
			prog: (e) => {
					if (!e.lengthComputable) return;
					let loded = Math.round(e.loaded / e.total * $.actSize);
					if ($.upState == 'chnk' && $.chnkNum) {		//console.log($.actSize,loded,e);
						//var loded = $.loaded;
						$.pBar.show(($.startByte + loded) / $.size);
						UpdateTotalProgress(loded - $.lastsz);
						$.lastsz = loded;
					} else if ($.upState == 'upld') {
						$.pBar.show(e.loaded / e.total);
						loded = Math.round(e.loaded / e.total * $.size);
						UpdateTotalProgress(loded - $.lastsz);
						$.lastsz = loded;
					}
				},
			//upload successful
			load: (pe) => {
					//console.log("QQQ",pe);
					//console.log('cbLoadErt '+pe.target.response);
					if (pe.target.status >= 400) {
						if (pe.target.status == 403) _qCtrl.stop();
						errOut(pe.target.response);
					//	$.pBar.msg(pe.target.response, true);
					//	endup();
					} else {
						if ($.doChnk) {
							cstate();
						} else {
							state();
						}
					}
				},
			abrt: () => {
					$.pBar.msg('-- Aborted', true);
					if ($.doChnk) {
						$.upState = 'abrt';
						cstate();
					} else {
						endup();
					}
				},
			//upload failure
			fail: () => {
					$.pBar.msg($.X.responseText, true);
					endup();
				}
			};

		$.abort = () => {
			if ($.X) {
				let xrs = $.X.readyState;
				if (xrs < 4 && xrs !== 0) {
					$.X.abort();
				} else {
					cb.abrt();
				}
			} else {
				$.pBar.rmov();
				$.pBar = null;
			}
		};

		// put up the progress bar
		$.pBar = new ProgressBar($, $.doChnk ? 'chnkpb' : 'normpb');

		let errM = '';
		if (!$.fType.match(/image\/|video\//)) {
			errM = 'File type is not allowed';
		} else if (typeof(aft) == 'object' && aft.length) {
			let dotParts = $.fn.split('.');
			if (dotParts.length == 1 || (aft.indexOf(dotParts.pop().toLowerCase()) < 0)) {
				errM = '<i class="fa fa-info-circle infoG" onclick="alert(\'Allowed file types: \' + H5uOpts.allowed_file_types.join(\', \'));"></i> File type not allowed';
			}
		} else if ($.size > opts.maxfilesize) {
			errM = 'File is larger than allowed';
		}

		if (errM) {
			UpdateTotalProgress($.size);
			errOut(errM);
			return;
		}

		const hndE = (e) => {
			//console.log(`${e.type}: ${e.loaded} bytes transferred\n`,e);
			//console.log('hndErt '+e.target.response);
			switch (e.type) {
				case 'progress':
					break;
				case 'abort':
					break;
				case 'error':
					break;
				case 'loadend':
					if (e.target.status == 200) {
						if ($.doChnk) {
							cstate();
						} else {
							state();
						}
					} else {
						$.pBar.msg(e.target.responseText, true);
					}
					break;
			}
		};

		$.X.addEventListener('loadstart', hndE);
	//	$.X.addEventListener('load', hndE);
	//	$.X.addEventListener('loadend', hndE);
//		$.X.addEventListener('progress', hndE);
		$.X.addEventListener('error', hndE);
		$.X.addEventListener('abort', hndE);

		$.X.onload = cb.load;
	//	$.X.upload.onload = cb.load;
		$.X.upload.onerror = cb.fail;
		$.X.onerror = cb.fail;
		$.X.upload.onabort = cb.abrt;
		$.X.upload.onprogress = cb.prog;

		if ($.doChnk) {
			cstate();
		} else {
			state();
		}

		return $;
	}

	let _setup = (h5uo) => {
		opts = Object.assign({}, opts, h5uo);
		let updiv = _id(opts.lodrdiv);
		if (w.File && w.FileList) {
			// create UI
			updiv.appendChild(CreateElement(
				'div',
				'<input type="file" name="userpictures" id="file_field" multiple="multiple" accept="image/*,video/*" style="display:none" />'
				+ '<div class="drpmsg">'+opts.dropMessage+'</div>',
				{id:'dropArea', onclick:"this.firstElementChild.click();"}
				)
			);
	
			let uprg = '<div id="progress_report_name"></div>'
				+'<div id="progress_report_status" style="font-style: italic;"></div>'
				+'<div id="totprogress">'
					+'<div id="progress_report_bar"></div>'
				+'</div>'
				+'<div class="quebar">'
					+'<div class="acti">Files queued: <span id="qcount">0</span></div>'
					+'<div class="acti">'
						+'<button id="qstop" class="btn btn-qaction btn-sm" title="stop queue" onclick="H5uQctrl.stop()" disabled><i class="fa fa-pause-circle pausQ"></i> Pause Queue</button>'
						+'<button id="qresume" class="btn btn-qaction btn-sm" title="resume queue" onclick="H5uQctrl.go()" disabled><i class="fa fa-play-circle playQ"></i> Resume Queue</button>'
						+'<button id="qcancel" class="btn btn-qaction btn-sm" title="cancel queue" onclick="H5uQctrl.cancel()" disabled><i class="fa fa-times cancelQ"></i> Cancel Queue</button>'
						+'</div>'
				+'</div>'
				+'<div id="fprogress"></div>'
				+'<div id="server_response"></div>';
			updiv.appendChild(CreateElement('div', uprg, {id:'progress_report', style:'position:relative'}));

			qCountSpan = _id('qcount');
			e_st = _id('qstop');
			e_rs = _id('qresume');
			e_cn = _id('qcancel');

			// get element to receive server response event data for each file
			fdonetarget = _id(opts.fdonetarget);

			// file select
			_ae(_id('file_field'), 'change', FileSelectHandler);

			// is XHR2 available?
			let xhr = new XMLHttpRequest();
			if (xhr.upload) {

				// file drop
				let filedrag = _id('dropArea');
				_ae(filedrag, 'dragover', FileDragHover);
				_ae(filedrag, 'dragleave', FileDragHover);
				_ae(filedrag, 'drop', FileSelectHandler);
				filedrag.style.display = 'block';

				// progress display area
				totProgressBar = _id('progress_report_bar');
				progressDiv = _id('fprogress');
			}
			xhr = null;

			// establish slicing function for chunking
			if ((typeof(Blob)!=='undefined')) {
				slfunc = (!!Blob.prototype.slice ? 'slice' : (!!Blob.prototype.webkitSlice ? 'webkitSlice' : (!!Blob.prototype.mozSlice ? 'mozSlice' : '')));
			}
		} else {
			updiv.appendChild(CreateElement('div', 'Can not use this upload method with the web browser that you are using.', {}));
		}
	};

	w.H5uSetup = _setup;
	w.H5uQctrl = _qCtrl;
	w.H5uOpts = opts;
	w.fupQadd2 = FileSelectHandler;


})(window, Joomla.getOptions('H5uOpts'));
