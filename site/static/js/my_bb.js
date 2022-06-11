/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

(function(My_bb) {

	let adlg = null,
		cdlg = null,
		pdlg = null,
		cbck = null;

	const amodal = '<div class="modal-dialog{S}">'+
			'<div class="modal-content">'+
				'<div class="modal-body"></div>'+
				'<div class="modal-footer"></div>'+
			'</div>'+
		'</div>';

	const but_can = '<button type="button" class="btn{C}" data-bs-dismiss="modal">{L}</button>';
	const but_cfm = '<button type="button" class="btn{C}" onclick="My_bb.cbck(event,this)">{L}</button>';

	const dialog = (wch, cls) => wch.replace('{S}', cls?(' '+cls):'');

	const createDlg = (opts) => {
		let mdl = document.createElement('div');
		mdl.setAttribute('role','dialog');
		mdl.setAttribute('tabindex','-1');
		mdl.className = 'joomla-modal modal fade';
		mdl.innerHTML = dialog(amodal, opts.size);
		document.body.appendChild(mdl);
		Joomla.initialiseModal(mdl, {isJoomla: true});
		return mdl;
	};

	const button = (wch, txt, cls) => wch.replace('{L}', txt).replace('{C}', cls?(' '+cls):'');

	const buttons = (buttons) => {
		let htm = '';	//button(but_can,'Cancel') + button(but_cfm,'Confirm');
		Object.entries(buttons).forEach((val) => {
			let wb = val[0];
			let bo = val[1];
			if (!bo.label) bo.label = wb;
			if (!bo.className) bo.className = 'btn-secondary';
			if (wb == 'cancel') htm += button(but_can, bo.label, bo.className);
			if (wb == 'confirm') htm += button(but_cfm, bo.label, bo.className);
		});
		return htm;
	};

	const show = (dlg, msg, buts) => {
		dlg.querySelector('.modal-body').innerHTML = msg;
		dlg.querySelector('.modal-footer').innerHTML = buts;
		dlg.open();
	};

	My_bb.close = () => {
		Joomla.Modal.getCurrent().close();
	};

	My_bb.cbck = (evt, elm) => {
		console.log(evt,elm);
		cbck(1);
		cdlg.close();
		//My_bb.close();
	};

	My_bb.alert = (msg, opts={}) => {
		if (!adlg) {
			adlg = createDlg(opts);
			adlg.addEventListener('shown.bs.modal', (e) => {adlg.querySelector('.btn-primary').focus()});
		}
		show(adlg, msg, button(but_can, Meedya._T('JOK'), 'btn-primary'));
	};

	My_bb.confirm = (opts) => {
		if (!cdlg) cdlg = createDlg(opts);
		cbck = opts.callback;
		show(cdlg, opts.message, buttons(opts.buttons));
	};

	My_bb.prompt = (opts) => {
		if (!pdlg) pdlg = createDlg(opts);
		cbck = opts.callback;
		show(pdlg, opts.message, buttons(opts.buttons));
	};

})(window.My_bb = window.My_bb || {});
