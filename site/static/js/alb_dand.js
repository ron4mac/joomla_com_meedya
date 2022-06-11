/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

// Need to have a separate Drag and Drop arranger for the gallery album hierarchy
Meedya.AArrange = (function (mdya, my) {
	let dragSrcEl = null,
		deTarg = null,
		iSlctd = null,
		scrt = false,
		scrb = false,
		scti = null,
		ctnr = '',
		meeid = 'meeid',
		items;

	// Private functions

	let setAlbPaid = (aid, paid, func) => {
		let prms = {task: 'manage.adjustAlbPaid', 'aid': aid, 'paid': paid};
		prms[Joomla.getOptions('csrf.token', '')] = 1;
		mdya.postAction(null, prms, (d) => func(d));
	};

	let dropable = (e) => {
		if (e.target == dragSrcEl) return false;
		let typs = e.dataTransfer.types;
		for (let i = 0; i < typs.length; ++i ) {
			if (typs[i] === meeid) return true;
		}
		return false;
	};

	let handleDragStart = (e) => {
//		e.target.classList.add('moving');
		dragSrcEl = e.target;
		setTimeout(()=>{dragSrcEl.style.opacity = '0.2'}, 0);
		e.dataTransfer.effectAllowed = 'copyMove';
		e.dataTransfer.setData(meeid,dragSrcEl.dataset.id);
		e.dataTransfer.dropEffect = 'move';
	};

	let handleDragOver = (e) => {
		e.dataTransfer.dropEffect = 'move';
		if (dropable(e)) {
			Meedya._pd(e);
			return false;
		}
	};

	let handleDragEnter = (e) => {
		e.dataTransfer.dropEffect = 'move';
		if (dropable(e)) {
			Meedya._pd(e);
			deTarg = e.target;
			e.target.classList.add('over');
			return false;
		}
	};

	let handleDragLeave = (e) => {
//		if (e.target == deTarg) {
			Meedya._pd(e);
			e.target.classList.remove('over');
//		}
	};

	let handleDrop = (e) => {
		Meedya._pd(e);
		// Don't do anything if dropping the same item we're dragging.
		if (dragSrcEl != e.target) {
			let sa = dragSrcEl.dataset.aid;
			let da = e.target.dataset.aid;
			setAlbPaid(sa, da, (r) => {
				if (r) {
					My_bb.alert(Meedya._T('COM_MEEDYA_MOVE_FAIL'));
				} else {
					e.target.append(dragSrcEl);
				}
			});
		}
		return false;
	};

	let handleDragEnd = (e) => {
		dragSrcEl.style.opacity = null;
		[].forEach.call(items, (itm) => itm.classList.remove('over'));
		e.target.classList.remove('moving');
		scrt = scrb = false;
		clearInterval(scti);
	};

	let handleDrag = (e) => {
		// scroll window if needed during drag
		if (!e.clientY) return;
		if (e.clientY < 50) {
			if (!scrt) {
				scrt = true;
				scti = setInterval(()=>window.scrollBy(0, -20), 20);
			}
		} else if (scrt) {
			scrt = false;
			clearInterval(scti);
		}
		if (e.clientY > (window.innerHeight - 50)) {
			if (!scrb) {
				scrb = true;
				scti = setInterval(()=>window.scrollBy(0, 20), 20);
			}
		} else if (scrb) {
			scrb = false;
			clearInterval(scti);
		}
	};

	let tMove = (e) => {
		if (e.targetTouches.length == 1) {
			let touch = e.targetTouches[0];
			// Place element where the finger is
			e.target.style.left = touch.pageX + 'px';
			e.target.style.top = touch.pageY + 'px';
		}
	};

	let iSelect = (e, elm=this) => {
		Meedya._pd(e);
		if (iSlctd) iSlctd.classList.remove('slctd');
		if (elm == iSlctd) {
			iSlctd = null;
		} else {
			iSlctd = elm;
			iSlctd.classList.add('slctd');
		}
	};

	// Return exported functions
	return {
		init: (iCtnr, iClass) => {
			ctnr = iCtnr;
			items = document.querySelectorAll('#'+iCtnr+' .'+iClass);
			[].forEach.call(items, (itm) => {
				//	itm.setAttribute('draggable', 'true');
					Meedya._ae(itm, 'drag', handleDrag);
					Meedya._ae(itm, 'dragstart', handleDragStart, true);
					Meedya._ae(itm, 'dragenter', handleDragEnter);
					Meedya._ae(itm, 'dragover', handleDragOver);
					Meedya._ae(itm, 'dragleave', handleDragLeave);
					Meedya._ae(itm, 'drop', handleDrop);
					Meedya._ae(itm, 'dragend', handleDragEnd);
					Meedya._ae(itm, 'touchmove', tMove);
				//	Meedya._ae(itm, 'click', iSelect);
				});
		},
		selalb: () => {
			return iSlctd ? iSlctd.dataset.aid : 0;
		},
		iSelect: iSelect
	};
}(Meedya, Joomla.getOptions('Meedya')));
