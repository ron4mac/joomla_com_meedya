/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
/* globals Joomla,URLSearchParams */
'use strict';

(function(Meedya, opts) {
	// getElementById
	Meedya._id = (id) => document.getElementById(id);

	// simplify cancelling an event
	Meedya._pd = (e, sp=true) => {
		if (e.preventDefault) { e.preventDefault(); }
		if (sp && e.stopPropagation) { e.stopPropagation(); }
	};
	// addEventListener
	Meedya._ae = (elm, evnt, func, capt=false) => {
		if (typeof elm === 'string') elm = Meedya._id(elm);
		elm.addEventListener(evnt, func, capt);
	};

	// get joomla text
	Meedya._T = (txt) => Joomla.Text._(txt);

	//build a FormData object
	const toFormData = (obj) => {
		const formData = new FormData();
		Object.keys(obj).forEach(key => {
			if (typeof obj[key] !== 'object') formData.append(key, obj[key]);
			else formData.append(key, JSON.stringify(obj[key]));
		});
		return formData;
	};

	// post ajax actions
	Meedya._P = (task, parms={}, cb=null, json=false, fini=null) => {
		if (typeof parms === 'object') {
			if (!(parms instanceof FormData)) parms = toFormData(parms);
		} else if (typeof parms === 'string') {
			parms = new URLSearchParams(parms);
		}
		if (task) parms.set('task', task);
	
		fetch(opts.rawURL, {method:'POST', body:parms})
		.then(resp => { if (!resp.ok) throw new Error(`HTTP ${resp.status}`); if (json) return resp.json(); else return resp.text() })
		.then(data => cb && cb(data))
		.catch(err => alert('Failure: '+err))
		.then(()=>fini && fini());
	};

	// open or close modals based on J4 or J3 bootstrap
	Meedya._oM = (elm) => {
		if (typeof elm === 'string') elm = Meedya._id(elm);
		elm.open ? elm.open() : jQuery(elm).modal('show');
	};
	Meedya._cM = (elm) => {
		if (!elm) { Joomla.Modal.getCurrent().close(); return; }
		if (typeof elm === 'string') elm = Meedya._id(elm);
		elm.close ? elm.close() : jQuery(elm).modal('hide');
	};

})(window.Meedya = window.Meedya || {}, Joomla.getOptions('Meedya'));
