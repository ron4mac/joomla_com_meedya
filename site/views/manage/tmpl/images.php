<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2017 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
JHtml::_('bootstrap.framework');
JHtml::stylesheet('components/com_meedya/static/vendor/blb/basicLightbox.min.css');
JHtml::stylesheet('components/com_meedya/static/css/manage.css');

function dateF ($dt)
{
	if (!$dt) return '';
	return date('M j, Y, g:i a', strtotime($dt));
}

?>
<script src="components/com_meedya/static/vendor/blb/basicLightbox.min.js"></script>
<script>
jQuery('#system-message-container').delay(5000).slideUp("slow");
var blb_path = "<?=JUri::root(true).'/'.$this->gallpath?>/med/";
var slctImgs = Array();
function editImg (iid) {
	window.location = "<?=JRoute::_('index.php?option=com_meedya')?>?task=manage.imgEdit&items="+iid;
}
function lboxPimg (iFile) {
	const src = blb_path + iFile;
	const html = '<img src="' + src + '">';
	basicLightbox.create(html).show();
}
function __editImg (evt, elm) {
	const pimg = elm.parentElement.previousElementSibling.firstElementChild;
	const iid = pimg.getAttribute('data-iid');
	window.location = "<?=JRoute::_('index.php?option=com_meedya')?>?task=manage.imgEdit&items="+iid;
}
function __lboxPimg (evt, elm) {
	console.log(elm.parentNode);
	const pimg = elm.parentElement.previousElementSibling.firstElementChild;
	const src = blb_path + pimg.getAttribute('data-img');
	const html = '<img src="' + src + '">';
	basicLightbox.create(html).show();
}
function lboxImg (evt, elm) {
	const src = blb_path + elm.getAttribute('data-img');
	const html = '<img src="' + src + '">';
	basicLightbox.create(html).show();
}
function slctImg (evt, elm) {
	if (jQuery(elm).hasClass('islct')) {
		lboxImg(evt, elm);
		//	const src = blb_path + elm.getAttribute('data-img');
		//	const html = '<img src="' + src + '">';
		//	basicLightbox.create(html).show();
	} else {
		if (!evt.metaKey) {
			for (var i = 0, len = slctImgs.length; i < len; i++) {
				jQuery(slctImgs[i]).removeClass('islct');
			}
			slctImgs = Array();
		}
		slctImgs.push(elm);
		jQuery(elm).addClass('islct');
	}
	return false;
}
function selAllImg (e, X) {
	e.preventDefault();
	var ck = X?'checked':'';
	var xbs = document.adminForm.elements["slctimg[]"];
	for (i = 0; i < xbs.length; i++) {
		xbs[i].checked = ck;
	}
}
function editSelected (e) {
	e.preventDefault();
	if (document.querySelectorAll("[name='slctimg[]']:checked").length) {
		document.adminForm.task.value = 'manage.imgsEdit';
		document.adminForm.submit();
	} else {
		alert("Please select some items first.");
	}
}
</script>
<style>
.mitem, .litem {width:120px; height:120px;}
.mitem {/*border:1px dashed transparent;*/}
.litem {cursor:pointer;}
.islct {border-color:blue;}
/* icons and buttons */
.action-icon {
	font-size: larger;
	margin-right: 0.5em;
}
.action-icon.acticon {
	color: rgba(51,51,51,1);
}
.action-icon.inaicon {
	color: rgba(51,51,51,0.5);
/*	cursor: pointer;*/
}
</style>
<div class="meedya-gallery">
	<?php if ($this->params->def('show_page_heading', 1)) : ?>
	<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
	<?php endif; ?>
	<form action="index.php?option=com_meedya&Itemid=<?php echo $this->itemId; ?>" method="post" name="adminForm" id="adminForm">
		<?php
			if ($this->mode == 'G') {
				echo '<a href="'.$this->linkUrl.'&mode=L"><span class="icon-list-2 action-icon inaicon" title="List View"> </span></a>';
				echo '<span class="icon-grid-2 action-icon acticon" title="Grid View"> </span>';
			} else {
				echo '<span class="icon-list-2 action-icon acticon" title="List View"> </span>';
				echo '<a href="'.$this->linkUrl.'&mode=G"><span class="icon-grid-2 action-icon inaicon" title="Grid View"> </span></a>';
			}
		?>
		<?php //echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
		<?php $fOpts = array('filterButton' => true); ?>
		<?php echo JLayoutHelper::render('search', array('view' => $this, 'options' => $fOpts), JPATH_ROOT.'/components/com_meedya/layouts'); ?>
		<div>
			<a href="#" title="select all images" onclick="selAllImg(event, true)">Select All</a>
			<a href="#" title="un-select all images" onclick="selAllImg(event, false)">Select None</a>
			<a href="javascript:void(0)" title="edit selected images" onclick="editSelected(event)">Edit selected images</a>
			<a href="#" title="remove selected from album" onclick="removeSelected(event)">Remove selected images from album</a>
		</div>
		<?php echo $this->loadTemplate($this->mode == 'G' ? 'grid' : 'list'); ?>
		<input type="hidden" name="task" value="manage.editImgs" />
		<input type="hidden" name="mode" value="<?=$this->mode?>" />
	</form>
</div>
<div class="page-footer">
	<?php echo $this->pagination->getListFooter(); ?>
</div>
<script>
	echo.init({
		baseUrl: "<?=JUri::root(true).'/'.$this->gallpath?>/",
		offset: 200,
		throttle: 250,
		debounce: false
	});
</script>