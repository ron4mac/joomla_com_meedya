// simplify cancelling an event
function _pd (e) {
	if (e.preventDefault) { e.preventDefault(); }
	if (e.stopPropagation) { e.stopPropagation(); }
}

// addEventListener
function _ae (elem, evnt, func) {
	elem.addEventListener(evnt, func);
}


var Arrange = (function () {
	var dragSrcEl = null,
		iSlctd = null,
		stop = true,
		ctnr = '',
		items;

	// Private functions

	function handleDragStart (e) {
		this.style.opacity = '0.4';  // this / e.target is the source node.

		dragSrcEl = this;

		e.dataTransfer.effectAllowed = 'move';
		e.dataTransfer.setData('text/html', this.innerHTML);
	}

	function handleDragOver (e) {
		_pd(e);

		e.dataTransfer.dropEffect = 'move';  // See the section on the DataTransfer object.

		return false;
	}

	function handleDragEnter (e) {
		// this / e.target is the current hover target.
		this.classList.add('over');
	}

	function handleDragLeave (e) {
		this.classList.remove('over');  // this / e.target is previous target element.
	}

	function handleDrop (e) {
		// this / e.target is current target element.
		_pd(e);

		// Don't do anything if dropping the same item we're dragging.
		if (dragSrcEl != this) {
			var area = document.getElementById(ctnr);
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
		this.style.opacity = '1.0';

		[].forEach.call(items, function (itm) {
			itm.classList.remove('over');
		});

		itmend.classList.remove('over');

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
		}
	};
}());





function allow_group_select_checkboxes(checkbox_wrapper_id){
	var lastChecked = null;
	var checkboxes = document.querySelectorAll('#'+checkbox_wrapper_id+' input[type="checkbox"]');

	//I'm attaching an index attribute because it's easy, but you could do this other ways
	for (var i=0;i<checkboxes.length;i++){
		checkboxes[i].setAttribute('data-index',i);
	}

	for (var i=0;i<checkboxes.length;i++){
		checkboxes[i].addEventListener("click",function(e){

			if(lastChecked && e.shiftKey) {
				var i = parseInt(lastChecked.getAttribute('data-index'));
				var j = parseInt(this.getAttribute('data-index'));
				var check_or_uncheck = this.checked;

				var low = i; var high=j;
				if (i>j){
					var low = j; var high=i;
				}

				for(var c=0;c<checkboxes.length;c++){
					if (low <= c && c <=high){
						checkboxes[c].checked = check_or_uncheck;
					}
				}
			}
			lastChecked = this;
		});
	}
}

/*
var dragSrcEl = null, iSlctd = null, aItems;

function handleDragStart (e) {
	this.style.opacity = '0.4';  // this / e.target is the source node.

	dragSrcEl = this;

	e.dataTransfer.effectAllowed = 'move';
	e.dataTransfer.setData('text/html', this.innerHTML);
}

function handleDragOver (e) {
	if (e.preventDefault) {
		e.preventDefault(); // Necessary. Allows us to drop.
	}

	e.dataTransfer.dropEffect = 'move';  // See the section on the DataTransfer object.

	return false;
}

function handleDragEnter (e) {
	// this / e.target is the current hover target.
	this.classList.add('over');
}

function handleDragLeave (e) {
	this.classList.remove('over');  // this / e.target is previous target element.
}

function handleDrop (e) {
	// this / e.target is current target element.

	if (e.stopPropagation) {
		e.stopPropagation(); // stops the browser from redirecting.
	}

	// Don't do anything if dropping the same item we're dragging.
	if (dragSrcEl != this) {
		var area = document.getElementById("area");
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
	this.style.opacity = '1.0';

	[].forEach.call(aItems, function (itm) {
		itm.classList.remove('over');
	});

	itmend.classList.remove('over');

	stop = true;
}

var stop = true;
function handleDrag (e) {
	stop = true;	//console.log(e);

	if (e.clientY < 50) {
		stop = false;
		scroll(-1)
	}

	if (e.clientY > ($(window).height() - 50)) {
		stop = false;
		scroll(1)
	}
}

var scroll = function (step) {
	var scrollY = $(window).scrollTop();
	$(window).scrollTop(scrollY + step);
	if (!stop) {
		setTimeout(function () { scroll(step) }, 60);
	}
}
*/
function handleAlbthmDragOver (e) {
	if (e.preventDefault) {
		e.preventDefault(); // Necessary. Allows us to drop.
	}

	e.dataTransfer.dropEffect = 'copy';  // See the section on the DataTransfer object.

	return false;
}

function handleAlbthmDrop (e)
{
	if (e.stopPropagation) {
		e.stopPropagation(); // stops the browser from redirecting.
	}

	console.log(e);
	this.style.opacity = '1.0';
}

/*
function arrangeSel (evt, elm) {
	var isel = elm;
	if (iSlctd) { iSlctd.classList.remove('iselect'); }
	if (isel == iSlctd) {
		iSlctd = null;
	} else {
		iSlctd = isel;
		iSlctd.classList.add('iselect');
	}
}
*/

function removeSelected (e) {
	e.preventDefault();
	var ids = [];
	$('.iselect').each(function(){ids.push($(this).data('iid'))});
//	console.log(ids);
//	console.log(aBaseURL+'manage.removeItems');
	$.post(aBaseURL+'manage.removeItems', {aid: albumID, items: ids.join("|")}, function (d) {
		if (!d) {
			$('.iselect').remove();
		} else {
			console.log(d);
		}
	});
}
/*
function editSelected (e) {
	e.preventDefault();
	var ids = [];
	$('.iselect').each(function(){ids.push($(this).data('iid'))});
	$('#aitems').val(ids.join("|"));
	document.getElementById("actform").submit();
}
*/
function arrangeSel (evt, elm) {
	if (elm.classList.contains('iselect')) {
		elm.classList.remove('iselect')
	} else {
		elm.classList.add('iselect')
	}
}
/*
function selAllImg (e) {
	e.preventDefault();
	$('.anitem').addClass('iselect');
}
function selNoImg (e) {
	e.preventDefault();
	$('.anitem').removeClass('iselect');
}
*/
/*
function tMove (e) {
	if (e.targetTouches.length == 1) {
		var touch = e.targetTouches[0];
		// Place element where the finger is
		this.style.left = touch.pageX + 'px';
		this.style.top = touch.pageY + 'px';
	}
}
*/

/*
var obj = document.getElementById('id');
obj.addEventListener('touchmove', function(event) {
	// If there's exactly one finger inside this element
	if (event.targetTouches.length == 1) {
		var touch = event.targetTouches[0];
		// Place element where the finger is
		obj.style.left = touch.pageX + 'px';
		obj.style.top = touch.pageY + 'px';
	}
}, false);
*/
/*
function initArrange () {
	aItems = document.querySelectorAll('#area .item');
	[].forEach.call(aItems, function(itm) {
	//		itm.setAttribute('draggable', 'true');
			itm.addEventListener('drag', handleDrag, false);
			itm.addEventListener('dragstart', handleDragStart, false);
			itm.addEventListener('dragenter', handleDragEnter, false);
			itm.addEventListener('dragover', handleDragOver, false);
			itm.addEventListener('dragleave', handleDragLeave, false);
			itm.addEventListener('drop', handleDrop, false);
			itm.addEventListener('dragend', handleDragEnd, false);
			itm.addEventListener('touchmove', tMove, false);
		});
	var albthm = document.getElementById("albthm");
	albthm.addEventListener('dragover', handleAlbthmDragOver, false);
	albthm.addEventListener('drop', handleAlbthmDrop, false);
	albthm.addEventListener('dragenter', function () { this.style.opacity = '0.5'; }, false);
	albthm.addEventListener('dragleave', function () { this.style.opacity = '1.0'; }, false);
	var itmend = document.getElementById("itmend");
	itmend.addEventListener('dragenter', handleDragEnter, false);
	itmend.addEventListener('dragover', handleDragOver, false);
	itmend.addEventListener('dragleave', handleDragLeave, false);
	itmend.addEventListener('drop', handleDrop, false);
	itmend.addEventListener('dragend', handleDragEnd, false);
}
*/