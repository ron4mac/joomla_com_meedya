var dragSrcEl = null, iSlctd = null, aItems;

function handleDragStart(e) {
	this.style.opacity = '0.4';  // this / e.target is the source node.

	dragSrcEl = this;

	e.dataTransfer.effectAllowed = 'move';
	e.dataTransfer.setData('text/html', this.innerHTML);
}

function handleDragOver(e) {
	if (e.preventDefault) {
		e.preventDefault(); // Necessary. Allows us to drop.
	}

	e.dataTransfer.dropEffect = 'move';  // See the section on the DataTransfer object.

	return false;
}

function handleDragEnter(e) {
	// this / e.target is the current hover target.
	this.classList.add('over');
}

function handleDragLeave(e) {
	this.classList.remove('over');  // this / e.target is previous target element.
}

function handleDrop(e) {
	// this / e.target is current target element.

	if (e.stopPropagation) {
		e.stopPropagation(); // stops the browser from redirecting.
	}

	// Don't do anything if dropping the same column we're dragging.
	if (dragSrcEl != this) {
		var area = document.getElementById("area");
		// Set the source column's HTML to the HTML of the column we dropped on.
	//	dragSrcEl.innerHTML = this.innerHTML;
	//	this.innerHTML = e.dataTransfer.getData('text/html');
		var orf = area.removeChild(dragSrcEl);
		area.insertBefore(orf, this);
	}

	return false;
}

function handleDragEnd(e) {
	// this/e.target is the source node.
	this.style.opacity = '1.0';

	[].forEach.call(aItems, function (col) {
		col.classList.remove('over');
	});

	itmend.classList.remove('over');
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

function editSelected (e) {
	e.preventDefault();
	var ids = [];
	$('.iselect').each(function(){ids.push($(this).data('iid'))});
	$('#aitems').val(ids.join("|"));
	document.getElementById("actform").submit();
}

function arrangeSel (evt, elm) {
	if (elm.classList.contains('iselect')) {
		elm.classList.remove('iselect')
	} else {
		elm.classList.add('iselect')
	}
}
function selAllImg (e) {
	e.preventDefault();
	$('.anitem').addClass('iselect');
}
function selNoImg (e) {
	e.preventDefault();
	$('.anitem').removeClass('iselect');
}

function initArrange() {
	aItems = document.querySelectorAll('#area .anitem');
	[].forEach.call(aItems, function(col) {
			col.setAttribute('draggable', 'true');
			col.addEventListener('dragstart', handleDragStart, false);
			col.addEventListener('dragenter', handleDragEnter, false);
			col.addEventListener('dragover', handleDragOver, false);
			col.addEventListener('dragleave', handleDragLeave, false);
			col.addEventListener('drop', handleDrop, false);
			col.addEventListener('dragend', handleDragEnd, false);
		});
	var itmend = document.getElementById("itmend");
	itmend.addEventListener('dragenter', handleDragEnter, false);
	itmend.addEventListener('dragover', handleDragOver, false);
	itmend.addEventListener('dragleave', handleDragLeave, false);
	itmend.addEventListener('drop', handleDrop, false);
	itmend.addEventListener('dragend', handleDragEnd, false);
}
