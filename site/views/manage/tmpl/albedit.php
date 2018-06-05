<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2018 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

MeedyaHelper::addStyle('basicLightbox', 'vendor/blb/');
MeedyaHelper::addStyle('gallery');
MeedyaHelper::addStyle('manage');
JHtml::_('jquery.framework');
//JHtml::_('jquery.framework', false);
//JHtml::_('jquery.ui', array('core', 'sortable'));
MeedyaHelper::addScript('manage');
MeedyaHelper::addScript('basicLightbox', 'vendor/blb/');
MeedyaHelper::addScript('bootbox');

$jdoc = JFactory::getDocument();
$jdoc->addScriptDeclaration('var baseURL = "'.JUri::base().'";
//var aBaseURL = "'.JUri::base().'index.php?option=com_meedya&format=raw&mID='.urlencode($this->meedyaID).'&task=";
var albumID = '.$this->aid.';
var blb_path = "'.JUri::root(true).'/'.$this->gallpath.'/med/";
');
//var_dump($this->album);
?>
<style>
	.mitem, .litem {width:120px; height:120px;}
	.modal-backdrop.fade.in {opacity:0.4}
	.bootbox-body {padding: 12px; font-size: larger;}
	.modal-footer {padding: 8px 10px}
</style>
<div class="meedya-gallery">
<?php if ($this->manage) echo JHtml::_('meedya.manageMenu', 1); ?>
<h1>- NEED SOME SORT OF HEADING HERE -</h1>
<button class="btn btn-primary" onclick="saveAlbum()">Save Changes</button>
<button class="btn" onclick="cancelEdt()">Cancel</button>
<form action="<?=JRoute::_('index.php?option=com_meedya')?>" id="albForm" name="albForm" method="POST">
	<div class="albman">
		<div class="albprp">
			Title:<br />
			<input type="text" name="albttl" value="<?=$this->album['title']?>" /><br />
			Description:<br />
			<textarea name="albdsc"><?=$this->album['desc']?></textarea>
			<input type="hidden" name="albthmid" id="albthmid" value=<?=$this->album['thumb']?> />
		</div>
		<div class="albthm" id="albthm">
			<img id="albthmimg" src="<?=$this->aThum?>" width="120px" height="120px" title="album thumbnail image" alt="album thumbnail" />
		</div>
	</div>
	<input type="hidden" name="task" value="manage.saveAlbum" />
	<input type="hidden" name="aid" value="<?=$this->aid?>" />
	<input type="hidden" name="thmord" value="" />
	<input type="hidden" name="referer" value="<?=base64_encode($this->referer)?>" />
	<?=JHtml::_('form.token')?>
</form>
<?php if ($this->params->def('show_page_heading', 1)) : ?>
<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
</h1>
<?php endif; ?>
<!-- <p>
	<?php //var_dump($this->items); ?>
</p> -->
<div class="actbuts">
	<!-- <button class="btn btn-mini" title="select all images" onclick="selAllImg(event, true)">Select All</button>
	<button class="btn btn-mini" title="un-select all images" onclick="selAllImg(event, false)">Select None</button>
	<button class="btn btn-mini" title="edit selected images" onclick="editSelected(event)"><i class="icon-pencil"></i> Edit selected images</button>
	<button class="btn btn-mini" title="remove selected from album" onclick="removeSelected(event)"><i class="icon-minus-circle"></i> Remove selected images from album</button> -->
	<?php echo JHtml::_('meedya.actionButtons', array('sela','seln','edts','rems')); ?>
</div>
<form id="actform" method="POST" action="<?=JRoute::_('index.php?option=com_meedya')?>" style="display:none">
	<input name="task" id="atask" type="hidden" value="manage.imgEdit" />
	<input name="items" id="aitems" type="hidden" value="" />
</form>
<form action="<?=JRoute::_('index.php?option=com_meedya')?>" method="POST" name="adminForm" id="adminForm">
<div id="area" style="display:flex;flex-wrap:wrap">
<?php
	foreach ($this->items as $item) {
		if (!$item) continue;
		echo JHtml::_('meedya.imageThumbElement', (object)$this->getItemFile($item), false, 'item');
	}
?>
	<div id="itmend" class="noitem item"></div>
</div>
<input type="hidden" name="task" value="manage.editImgs" />
</form>
</div>



  <style>
  #sortable { list-style-type: none; margin: 0; padding: 0; /*width: 450px;*/ }
  #sortable li { margin: 3px 3px 3px 0; padding: 1px; float: left; width: 100px; height: 90px; font-size: 5em; text-align: center; border: 1px solid #EEE; line-height: normal; background-color: #EEF; }
  </style>
<ul id="sortable">
  <li class="ui-state-default">1</li>
  <li class="ui-state-default">2</li>
  <li class="ui-state-default">3</li>
  <li class="ui-state-default">4</li>
  <li class="ui-state-default">5</li>
  <li class="ui-state-default">6</li>
  <li class="ui-state-default">7</li>
  <li class="ui-state-default">8</li>
  <li class="ui-state-default">9</li>
  <li class="ui-state-default">10</li>
  <li class="ui-state-default">11</li>
  <li class="ui-state-default">12</li>
</ul>



<div class="page-footer">
	<?php echo $this->pagination->getListFooter(); ?>
</div>
<script>

//  jQuery( function() {
//    jQuery( "#sortable" ).sortable();
//    jQuery( "#sortable" ).disableSelection();
//  } );





function cancelEdt () {
	window.location = atob(document.albForm.referer.value);
}
echo.init({
	baseUrl: "<?=JUri::root(true).'/'.$this->gallpath?>/",
	offset: 200,
	throttle: 250,
	debounce: false
});

Arrange.init('area','item');

var albthm = document.getElementById("albthm");
albthm.addEventListener('dragover', handleAlbthmDragOver, false);
albthm.addEventListener('drop', handleAlbthmDrop, false);
albthm.addEventListener('dragenter', function () { this.style.opacity = '0.5'; }, false);
albthm.addEventListener('dragleave', function () { this.style.opacity = '1.0'; }, false);
var albfrm = document.getElementById("albForm");
albfrm.addEventListener('dragstart', function(e){ e.dataTransfer.setData('albthm','X'); }, false);
albfrm.addEventListener('dragover', function(e){ if (e.dataTransfer.types.indexOf('albthm')>0) { _pd(e);e.dataTransfer.dropEffect = 'move'; } }, false);
albfrm.addEventListener('dragenter', function(e){ if (e.dataTransfer.types.indexOf('albthm')>0) { _pd(e);e.dataTransfer.dropEffect = 'move'; } }, false);
albfrm.addEventListener('drop', function(e){ _pd(e); removeAlbThm(); }, false);
</script>