/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
/* based on echo-js v1.7.2 | (c) 2016 @toddmotto | https://github.com/toddmotto/echo */
(function (root, factory) {
	if (typeof define === 'function' && define.amd) {
		define(function() {
			return factory(root);
		});
	} else if (typeof exports === 'object') {
		module.exports = factory;
	} else {
		root.echo = factory(root);
	}
})(this, function (root) {

	'use strict';

	// use convenience/minification constants
	/** @noinline */
	const	DE = 'data-echo';
	/** @noinline */
	const	DEBG = 'data-echo-background';
	/** @noinline */
	const	DEPH = 'data-echo-placeholder';

	let echo = {};

	let callback = function () {};

	let offset, poll, delay, useDebounce, unload, baseUrl;

	const isHidden = (element) => (element.offsetParent === null);

	const inView = (element, view) => {
		if (isHidden(element)) {
			return false;
		}
		let box = element.getBoundingClientRect();
		return (box.right >= view.l && box.bottom >= view.t && box.left <= view.r && box.top <= view.b);
	};

	const debounceOrThrottle = () => {
		if(!useDebounce && !!poll) {
			return;
		}
		clearTimeout(poll);
		poll = setTimeout(() => { echo.render(); poll = null; }, delay);
	};

	echo.init = (opts) => {
		opts = opts || {};
		let offsetAll = opts.offset || 0;
		let offsetVertical = opts.offsetVertical || offsetAll;
		let offsetHorizontal = opts.offsetHorizontal || offsetAll;
		let optionToInt = (opt, fallback) => parseInt(opt || fallback, 10);
		baseUrl = opts.baseUrl || '';
		offset = {
			t: optionToInt(opts.offsetTop, offsetVertical),
			b: optionToInt(opts.offsetBottom, offsetVertical),
			l: optionToInt(opts.offsetLeft, offsetHorizontal),
			r: optionToInt(opts.offsetRight, offsetHorizontal)
		};
		delay = optionToInt(opts.throttle, 250);
		useDebounce = opts.debounce !== false;
		unload = !!opts.unload;
		callback = opts.callback || callback;
		echo.render();
		if (document.addEventListener) {
			root.addEventListener('scroll', debounceOrThrottle);
			root.addEventListener('load', debounceOrThrottle);
			window.addEventListener('resize', debounceOrThrottle/*function(){ echo.render() }*/);
		} else {
			root.attachEvent('onscroll', debounceOrThrottle);
			root.attachEvent('onload', debounceOrThrottle);
			window.attachEvent('resize', debounceOrThrottle/*function(){ echo.render() }*/);
		}
	};

	echo.render = (context) => {
		let nodes = (context || document).querySelectorAll(`[${DE}], [${DEBG}]`);
		let length = nodes.length;
		let src, elem;
		let view = {
			l: 0 - offset.l,
			t: 0 - offset.t,
			b: (root.innerHeight || document.documentElement.clientHeight) + offset.b,
			r: (root.innerWidth || document.documentElement.clientWidth) + offset.r
		};
		for (let i = 0; i < length; i++) {
			elem = nodes[i];
			if (inView(elem, view)) {

				if (unload) {
					elem.setAttribute(DEPH, elem.src);
				}

				if (elem.getAttribute(DEBG) !== null) {
					elem.style.backgroundImage = 'url(' + elem.getAttribute(DEBG) + ')';
				} else if (elem.src !== (src = (baseUrl + elem.getAttribute(DE)))) {
					elem.src = src;
				}

				if (!unload) {
					elem.removeAttribute(DE);
					elem.removeAttribute(DEBG);
				}

				callback(elem, 'load');
			} else if (unload && !!(src = elem.getAttribute(DEPH))) {

				if (elem.getAttribute(DEBG) !== null) {
					elem.style.backgroundImage = 'url(' + src + ')';
				} else {
					elem.src = src;
				}

				elem.removeAttribute(DEPH);
				callback(elem, 'unload');
			}
		}
		if (!length) {
			echo.detach();
		}
	};

	echo.detach = () => {
		if (document.removeEventListener) {
			root.removeEventListener('scroll', debounceOrThrottle);
		} else {
			root.detachEvent('onscroll', debounceOrThrottle);
		}
		clearTimeout(poll);
	};

	return echo;

});
