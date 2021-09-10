if (typeof Meedya === 'undefined') {
	Meedya = {};	// a namespace for com_meedya
}

/* a few utility functions to avoid using jquery and assist in minification */
// getElementById
function _id (id) {
	return document.getElementById(id);
}
// simplify cancelling an event
function _pd (e,sp=true) {
	if (e.preventDefault) { e.preventDefault(); }
	if (sp && e.stopPropagation) { e.stopPropagation(); }
}
// addEventListener
function _ae (elem, evnt, func, capt=false) {
	elem.addEventListener(evnt, func, capt);
}


(function($) {

	Meedya.setDlgParAlb = function () {
		if (_id('h5u_palbum'))
		_id('h5u_palbum').value = Meedya.AArrange.selalb();
	};

	Meedya.setAlbumDanD = function () {
		var albthm = _id("albthm");
		_ae(albthm, 'dragover', handleAlbthmDragOver, false);
		_ae(albthm, 'drop', handleAlbthmDrop, false);
		_ae(albthm, 'dragenter', function () { this.style.opacity = '0.5'; }, false);
		_ae(albthm, 'dragleave', function () { this.style.opacity = '1.0'; }, false);
		var albfrm = _id("albForm");
		_ae(albfrm, 'dragstart', function(e){ e.dataTransfer.setData('albthm','X'); }, false);
		_ae(albfrm, 'dragover', function(e){ if (e.dataTransfer.types.indexOf('albthm')>0) { _pd(e);e.dataTransfer.dropEffect = 'move'; } }, false);
		_ae(albfrm, 'dragenter', function(e){ if (e.dataTransfer.types.indexOf('albthm')>0) { _pd(e);e.dataTransfer.dropEffect = 'move'; } }, false);
		_ae(albfrm, 'drop', function(e){ _pd(e); removeAlbThm(); }, false);
	};

	Meedya.deleteSelected = function (e) {
		_pd(e);
		if (hasSelections("[name='slctimg[]']:checked", true)) {
			bootbox.confirm({
				message: Joomla.JText._('COM_MEEDYA_PERM_DELETE'),
				buttons: {
					confirm: { label: 'JACTION_DELETE', className: 'btn-danger' },
					cancel: { label: 'JCANCEL' }
				},
				callback: function(c){
					if (c) {
						document.adminForm.task.value = 'manage.deleteItems';
						document.adminForm.submit();
					}
				}
			});
		}
	};

	Meedya.removeSelected = function (e) {
		_pd(e);
		if (hasSelections("[name='slctimg[]']:checked", true)) {
			bootbox.confirm({
				message: Joomla.JText._('COM_MEEDYA_REMOVE'),
				buttons: {
					confirm: { label: 'COM_MEEDYA_VRB_REMOVE', className: 'btn-danger' },
					cancel: { label: 'JCANCEL' }
				},
				callback: function(c){
					if (c) {
						var items = document.querySelectorAll("[name='slctimg[]']:checked");
						var pnode = items[0].parentNode.parentNode;
						for (var i=0; i<items.length; i++) {
							pnode.removeChild(items[i].parentNode);
						}
					}
				}
			});
		}
	};

	Meedya.selAllImg = function (e, X) {
		_pd(e);
		var ck = X?'checked':'';
		var xbs = document.adminForm.elements["slctimg[]"];
		// make up for no array returned if there is only one item
		if (!xbs.length) xbs = [xbs];
		for (var i = 0; i < xbs.length; i++) {
			xbs[i].checked = ck;
		}
	};

	Meedya.editSelected = function (e) {
		_pd(e);
		if (hasSelections("input[name='slctimg[]']:checked",true)) {
			document.adminForm.task.value = 'manage.imgsEdit';
			document.adminForm.submit();
		}
	};

	Meedya.addSelected = function (e) {
		_pd(e);
		if (hasSelections("input[name='slctimg[]']:checked",true)) {
			$('#add2albdlg').modal('show');
		}
	};

	Meedya.saveAlbum = function () {
		document.albForm.thmord.value = Meedya.Arrange.iord();
		document.albForm.submit();
	};




	// watch for selection of album; enable create button when there is one
	Meedya.watchAlb = function (elm) {
		var creab = _id('creab');
		var classes = creab.classList;
		if (elm.value > 0) {
			_id('creanualb').style.display = "none";
			classes.remove("btn-disabled");
			classes.add("btn-primary");
			creab.disabled = false;
		} else {
			classes.remove("btn-primary");
			classes.add("btn-disabled");
			creab.disabled = true;
			if (elm.value == -1) {
				_id('creanualb').style.display = "block";
			} else {
				_id('creanualb').style.display = "none";
			}
		}
	};

	// watch for entry of album name; enable create button when there is a name
	Meedya.watchAlbNam = function (elm) {
		//var creab = _id('creab');	console.log(creab,elm.value);
		var creab = _id('creab');
		var classes = creab.classList;
		if (elm.value.trim()) {
			classes.remove("btn-disabled");
			classes.add("btn-primary");
			creab.disabled = false;
		} else {
			classes.remove("btn-primary");
			classes.add("btn-disabled");
			creab.disabled = true;
		}
	};

	Meedya.addItems2Album = function (elm) {
		elm.disabled = true;
		document.adminForm.albumid.value = _id('h5u_album').value;
		document.adminForm.nualbnam.value = _id('nualbnam').value;
		document.adminForm.nualbpar.value = _id('h5u_palbum').value;
		document.adminForm.nualbdesc.value = _id('albdesc').value;
		document.adminForm.task.value = 'manage.addItemsToAlbum';
		document.adminForm.submit();
	};

	Meedya.aj_addItems2Album = function (elm) {
		elm.disabled = true;
		var albNamFld = _id('nualbnam');
		var albParFld = _id('h5u_palbum');
		var albDscFld = _id('albdesc');
		var nualbnam = albNamFld.value.trim();
		var ajd = {task: 'manage.addItemsToAlbum', albnam: nualbnam, paralb: (albParFld ? albParFld.value : 0), albdesc: albDscFld.value};
		ajd[Meedya.formTokn] = 1;
		$.post(Meedya.rawURL, ajd,
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
	Meedya.ae_createAlbum = function (elm) {
		elm.disabled = true;
		var albNamFld = _id('nualbnam');
		var albParFld = _id('h5u_palbum');
		var albDscFld = _id('albdesc');
		var nualbnam = albNamFld.value.trim();
		var ajd = {task: 'manage.newAlbum', albnam: nualbnam, paralb: (albParFld ? albParFld.value : 0), albdesc: albDscFld.value};
		ajd[Meedya.formTokn] = 1;
		$.post(Meedya.rawURL, ajd,
			function (response, status, xhr) {
				//console.log(response, status, xhr);
				if (status=="success") {
					jQuery('#newalbdlg').modal('hide');
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


	// rearrange items in an album
	var moving = null;
	Meedya.moveItem = function (elm) {
		var item = elm.parentElement;
		console.log(item);
		if (!moving) {
			moving = item;
			item.classList.add("moving");
		} else {
			moving.classList.remove("moving");
			if (item != moving) {
				var area = _id('area');
				var orf = area.removeChild(moving);
				area.insertBefore(orf, item);
			}
			moving = null;
		}
	};



	// %%% private functions %%%

	function removeAlbThm () {
		_id('albthmimg').src = 'components/com_meedya/static/img/img.png';
		_id('albthmid').value = 0;
	}

	function handleAlbthmDragOver (e) {
		if (e.dataTransfer.types.indexOf('imgsrc') < 0) return;
		_pd(e);		 // Necessary. Allows us to drop.
		e.dataTransfer.dropEffect = 'copy';		// See the section on the DataTransfer object.
		return false;
	}

	function handleAlbthmDrop (e) {
		_pd(e);		// stops the browser from redirecting.
		var src = e.dataTransfer.getData('imgsrc');
		if (src) {
			this.getElementsByTagName("IMG")[0].src = src;
			var atv = _id('albthmid');
			atv.value = e.dataTransfer.getData('meeid');
		}
		this.style.opacity = '1.0';
	}

	function hasSelections (sel, alrt=false) {
		if (document.querySelectorAll(sel).length) {
			return true;
		} else {
			if (alrt) bootbox.alert(Joomla.JText._('COM_MEEDYA_SELECT_SOME'));
			return false;
		}
	}

})(jQuery);


Meedya.Arrange = (function ($) {
	var dragSrcEl = null,
		iSlctd = null,
		stop = true,
		ctnr = '',
		clas = '',
		meeid = 'meeid',
		items;

	// Private functions
/*
	function contains(list, value) {
		for ( var i = 0; i < list.length; ++i ) {
			if (list[i] === value) return true;
		}
		return false;
	}
*/
	function hasItem (e) {
		var typs = e.dataTransfer.types;
		for (var i = 0; i < typs.length; ++i ) {
			if (typs[i] === meeid) return true;
		}
		return false;
	}

	function handleDragStart (e) {
		this.style.opacity = '0.4';		// this / e.target is the source node.

		dragSrcEl = this;
		e.dataTransfer.effectAllowed = 'move';
	//	e.dataTransfer.setData('text/html', this.innerHTML);
		e.dataTransfer.setData(meeid,this.getAttribute('data-id'));
	//	console.log(this.getElementsByTagName("IMG")[0].src);
		e.dataTransfer.setData('imgsrc',this.getElementsByTagName("IMG")[0].src);
	}

	function handleDragOver (e) {
		if (hasItem(e)) {
			_pd(e);
			e.dataTransfer.dropEffect = 'move';		// See the section on the DataTransfer object.
			return false;
		}
	}

	function handleDragEnter (e) {
		// this / e.target is the current hover target.
		if (hasItem(e)) {
			_pd(e);
			this.classList.add('over');
			return false;
		}
	}

	function handleDragLeave (e) {
		this.classList.remove('over');		// this / e.target is previous target element.
	}

	function handleDrop (e) {
		// this / e.target is current target element.
		_pd(e);
		// Don't do anything if dropping the same item we're dragging.
		if (/*e.dataTransfer.types.contains(meeid) &&*/ dragSrcEl != this) {
//			console.log(dragSrcEl);
//			console.log(e);
			var area = _id(ctnr);
			// Set the source item's HTML to the HTML of the item we dropped on.
		//	dragSrcEl.innerHTML = this.innerHTML;
		//	this.innerHTML = e.dataTransfer.getData('text/html');
			var orf = area.removeChild(dragSrcEl);
			area.insertBefore(orf, this);
		}

		return false;
	}

	function handleDragEnd (e) {
		// this/e.target is the source node.
		this.style.opacity = null;

		[].forEach.call(items, function (itm) {
			itm.classList.remove('over');
		});

//		itmend.classList.remove('over');

		stop = true;
	}

	function handleDrag (e) {
		stop = true;	//console.log(e);

		if (e.clientY < 50) {
			stop = false;
			scroll(-1);
		}

		if (e.clientY > ($(window).height() - 50)) {
			stop = false;
			scroll(1);
		}
	}

	function tMove (e) {
		if (e.targetTouches.length == 1) {
			var touch = e.targetTouches[0];
			// Place element where the finger is
			this.style.left = touch.pageX + 'px';
			this.style.top = touch.pageY + 'px';
		}
	}

	var scroll = function (step) {
		var scrollY = $(window).scrollTop();
		$(window).scrollTop(scrollY + step);
		if (!stop) {
			setTimeout(function () { scroll(step) }, 60);
		}
	};

	// Return exported functions
	return {
		init: function (iCtnr, iClass) {
			ctnr = iCtnr;
			clas = iClass;
			items = document.querySelectorAll('#'+iCtnr+' .'+iClass);
			[].forEach.call(items, function(itm) {
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
		iord: function () {
			items = document.querySelectorAll('#'+ctnr+' .'+clas);
			var imord = [];
			[].forEach.call(items, function (itm) {
				var iid = itm.getAttribute('data-id');
				if (iid) imord.push(iid);
			});
			return imord.join("|");
		}
	};
}(jQuery));


// Need to have a separate Drag and Drop arranger for the gallery album hierarchy

Meedya.AArrange = (function ($) {
	var dragSrcEl = null,
		deTarg = null,
		iSlctd = null,
		stop = true,
		ctnr = '',
		meeid = 'meeid',
		items;

	// Private functions

	function setAlbPaid (aid, paid, func) {

	//	func(''); alert("Actual album move is disabled"); return;

	//	var prms = {'aid': aid, 'paid': paid};
		var prms = {task: 'manage.adjustAlbPaid', 'aid': aid, 'paid': paid};
		prms[Joomla.getOptions('csrf.token', '')] = 1;
		$.post(Meedya.rawURL, prms, function (d) {
			func(d);
		});
	}

	function hasItem (e) {
		var typs = e.dataTransfer.types;
		for (var i = 0; i < typs.length; ++i ) {
			if (typs[i] === meeid) return true;
		}
		return false;
	}

	function handleDragStart (e) {
		e.target.style.opacity = '0.4';

		dragSrcEl = this;
		e.dataTransfer.effectAllowed = 'copyMove';
	//	e.dataTransfer.setData('text/html', this.innerHTML);
		e.dataTransfer.setData(meeid,this.getAttribute('data-id'));
	//	console.log(this.getElementsByTagName("IMG")[0].src);
//		e.dataTransfer.setData('imgsrc',this.getElementsByTagName("IMG")[0].src);
	}

	function handleDragOver (e) {
		if (hasItem(e)) {
			_pd(e);
			e.dataTransfer.dropEffect = 'move';		// See the section on the DataTransfer object.
			return false;
		}
	}

	function handleDragEnter (e) {
		// this / e.target is the current hover target.
//		if (hasItem(e)) {
			_pd(e);
			deTarg = e.target;
			this.classList.add('over');
			return false;
//		}
	}

	function handleDragLeave (e) {
//		if (e.target == deTarg) {
			_pd(e);
			this.classList.remove('over');		// this / e.target is previous target element.
//		}
	}

	function handleDrop (e) {
		// this / e.target is current target element.
		_pd(e);
		// Don't do anything if dropping the same item we're dragging.
		if (/*e.dataTransfer.types.contains(meeid) &&*/ dragSrcEl != this) {
//			console.log(dragSrcEl);
//			console.log(e);
		sa = dragSrcEl.getAttribute('data-aid');
		da = e.target.getAttribute('data-aid');
		setAlbPaid(sa, da, function(r){
			if (r) {
				bootbox.alert(Joomla.JText._('COM_MEEDYA_MOVE_FAIL'));
			} else {
				e.target.append(dragSrcEl);
			}
		});
//		e.target.append(dragSrcEl);
//			var area = _id(ctnr);
			// Set the source item's HTML to the HTML of the item we dropped on.
		//	dragSrcEl.innerHTML = this.innerHTML;
		//	this.innerHTML = e.dataTransfer.getData('text/html');
//			var orf = area.removeChild(dragSrcEl);
//			area.insertBefore(orf, this);
		}

		return false;
	}

	function handleDragEnd (e) {
		// this/e.target is the source node.
		this.style.opacity = '1.0';

		[].forEach.call(items, function (itm) {
			itm.classList.remove('over');
		});

		stop = true;
	}

	function handleDrag (e) {
		stop = true;

		if (e.clientY < 50) {
			stop = false;
			scroll(-1);
		}

		if (e.clientY > ($(window).height() - 50)) {
			stop = false;
			scroll(1);
		}
	}

	function tMove (e) {
		if (e.targetTouches.length == 1) {
			var touch = e.targetTouches[0];
			// Place element where the finger is
			this.style.left = touch.pageX + 'px';
			this.style.top = touch.pageY + 'px';
		}
	}

	function iSelect (e) {
		//console.log(this);
		_pd(e);
		if (iSlctd) iSlctd.classList.remove('slctd');
		if (this == iSlctd) {
			iSlctd = null;
		} else {
			iSlctd = this;
			iSlctd.classList.add('slctd');
		}
	}

	var scroll = function (step) {
		var scrollY = $(window).scrollTop();
		$(window).scrollTop(scrollY + step);
		if (!stop) {
			setTimeout(function () { scroll(step) }, 500);
		}
	};

	// Return exported functions
	return {
		init: function (iCtnr, iClass) {
			ctnr = iCtnr;
			items = document.querySelectorAll('#'+iCtnr+' .'+iClass);
			[].forEach.call(items, function(itm) {
			//		itm.setAttribute('draggable', 'true');
					_ae(itm, 'drag', handleDrag);
					_ae(itm, 'dragstart', handleDragStart, true);
					_ae(itm, 'dragenter', handleDragEnter);
					_ae(itm, 'dragover', handleDragOver);
					_ae(itm, 'dragleave', handleDragLeave);
					_ae(itm, 'drop', handleDrop);
					_ae(itm, 'dragend', handleDragEnd);
					_ae(itm, 'touchmove', tMove);
					_ae(itm, 'click', iSelect);
				});
		},
		selalb: function () {
			return iSlctd ? iSlctd.getAttribute('data-aid') : 0;
		}
	};
}(jQuery));


// iZoom action to expand individual image
(function ($, w) {
	var back, area;

	function keyPressed (e) {
		switch (e.charCode) {
			case 32:
			case 13:
			case 27:
				close(e);
				break;
			default:
				break;
		}
	}

	function keyDowned (e) {
		switch (e.keyCode) {
			case 32:
			case 13:
			case 27:
				close(e);
				break;
			default:
				break;
		}
	}

	function open (pID) {
		area = document.createElement('div');
		$.post(Meedya.rawURL, {task: 'manage.getZoomItem', iid: pID},
			function (data) {
				area.innerHTML = data;	// + '<div class="zoom-closex" onclick="iZoomClose(event)">X</div>';
			});
		area.className = 'zoom-area';
		area.tabIndex = "-1";
		back = document.createElement('div');
		back.className = 'zoom-back';
		back.appendChild(area);
		document.body.appendChild(back);
		_ae(area, 'keypress', keyPressed, false);
		_ae(area, 'keydown', keyDowned, false);
		area.focus();
		_ae(back, 'click', close, false);
	}

	function close (e) {
		_pd(e);
		document.body.removeChild(back);
	}

	w.iZoomOpen = open;
	w.iZoomClose = close;

})(jQuery, window);
