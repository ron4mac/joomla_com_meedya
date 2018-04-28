<?php
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

$jdoc = JFactory::getDocument();
$jdoc->addStyleSheet('components/com_meedya/static/vendor/blb/basicLightbox.min.css');
$jdoc->addStyleSheet('components/com_meedya/static/css/gallery.css'.$this->bgt);
$jdoc->addStyleSheet('components/com_meedya/static/css/manage.css'.$this->bgt);
JHtml::_('jquery.framework', false);
//$jdoc->addScript('components/com_meedya/static/js/manage.js'.$this->bgt);
MeedyaHelper::addScript('manage');
//$jdoc->addScript('components/com_meedya/static/vendor/blb/basicLightbox.min.js');
MeedyaHelper::addScript('basicLightbox', 'vendor/blb/');
//$jdoc->addScript('components/com_meedya/static/vendor/blb/main.js');

$jdoc->addScriptDeclaration('var baseURL = "'.JUri::base().'";
//var aBaseURL = "'.JUri::base().'index.php?option=com_meedya&format=raw&mID='.urlencode($this->meedyaID).'&task=";
var albumID = '.$this->aid.';
var blb_path = "'.JUri::root(true).'/'.$this->gallpath.'/med/";
');
//var_dump($this->album);
?>
<style>
	.mitem, .litem {width:120px; height:120px;}
</style>
<div class="meedya-gallery">
<?php if ($this->manage) echo JHtml::_('meedya.manageMenu', 1); ?>
<h1>- NEED SOME SORT OF HEADING HERE -</h1>
<div class="albman">
	<div class="albprp">
		Title:<br />
		<input type="text" name="albttl" value="<?=$this->album['title']?>" /><br />
		Description:<br />
		<textarea name="albdsc"><?=$this->album['desc']?></textarea>
		<input type="hidden" name="albthmid" id="albthmid" value=<?=$this->album['thumb']?> />
	</div>
	<div class="albthm" id="albthm">
		<img src="<?=$this->aThum?>" width="120px" height="120px" title="album thumbnail image" alt="album thumbnail" />
	</div>
</div>
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

Arrange.init('area','item');

var albthm = document.getElementById("albthm");
albthm.addEventListener('dragover', handleAlbthmDragOver, false);
albthm.addEventListener('drop', handleAlbthmDrop, false);
albthm.addEventListener('dragenter', function () { this.style.opacity = '0.5'; }, false);
albthm.addEventListener('dragleave', function () { this.style.opacity = '1.0'; }, false);
</script>