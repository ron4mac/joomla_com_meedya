/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

Meedya.Arrange = (function (mdya) {
	let dragSrcEl = null,
		scrt = false,
		scrb = false,
		scti = null,
		ctnr = '',
		clas = '',
		meeid = 'meeid',
		items;

	// Private functions
	let dropable = (e) => {
		if (e.target.parentElement.parentElement == dragSrcEl) return false;
		let typs = e.dataTransfer.types;
		for (let i = 0; i < typs.length; ++i ) {
			if (typs[i] === meeid) return true;
		}
		return false;
	};

	let handleDragStart = (e) => {
		dragSrcEl = e.target.parentElement.parentElement;
		setTimeout(()=>{dragSrcEl.style.opacity = '0.2'}, 0);
//		e.dataTransfer.effectAllowed = 'copyMove';
		e.dataTransfer.effectAllowed = 'move';
		e.dataTransfer.dropEffect = 'move';
		e.dataTransfer.setData(meeid,dragSrcEl.dataset.id);
		e.dataTransfer.setData('imgsrc',e.target.src);
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

	let handleDragEnd = (e) => {
		dragSrcEl.style.opacity = null;
		[].forEach.call(items, (itm) => itm.classList.remove('over'));
		scrt = scrb = false;
		clearInterval(scti);
	};

	let handleDrop = (e) => {
		Meedya._pd(e);
		let dtarg = e.target.parentElement.parentElement;
		// Don't do anything if dropping the same item we're dragging.
		if (dragSrcEl != dtarg) {
			let area = Meedya._id(ctnr);
			let orf = area.removeChild(dragSrcEl);
			area.insertBefore(orf, dtarg);
			mdya.dirtyThumbs(true);
		}
		return false;
	};

	let handleDragEnter = (e) => {
			e.dataTransfer.dropEffect = 'move';
		if (dropable(e)) {
			Meedya._pd(e);
			e.target.classList.add('over');
			return false;
		}
	};

	let handleDragOver = (e) => {
			e.dataTransfer.dropEffect = 'move';
		if (dropable(e)) {
			Meedya._pd(e);
			e.dataTransfer.dropEffect = 'move';
			return false;
		}
	};

	let handleDragLeave = (e) => {
		e.target.classList.remove('over');
	};

	// Return exported functions
	return {
		init: (iCtnr, iClass) => {
			ctnr = iCtnr;
			clas = iClass;
		//	items = document.querySelectorAll('#'+iCtnr+' .'+iClass);
			items = document.querySelectorAll('#'+iCtnr+' img');
			[].forEach.call(items, (itm) => {
			//		itm.setAttribute('draggable', 'true');
					Meedya._ae(itm, 'drag', handleDrag);
					Meedya._ae(itm, 'dragstart', handleDragStart);
					Meedya._ae(itm, 'dragenter', handleDragEnter);
					Meedya._ae(itm, 'dragover', handleDragOver);
					Meedya._ae(itm, 'dragleave', handleDragLeave);
					Meedya._ae(itm, 'drop', handleDrop);
					Meedya._ae(itm, 'dragend', handleDragEnd);
					Meedya._ae(itm, 'touchmove', tMove);
				});
		},
		iord: () => {
		//	items = document.querySelectorAll('#'+ctnr+' .'+clas);
			items = document.querySelectorAll('#'+ctnr+' .item');
			let imord = [];
			[].forEach.call(items, (itm) => {
				let iid = itm.dataset.id;
				if (iid) imord.push(iid);
			});
			return imord.join("|");
		}
	};
}(Meedya));
