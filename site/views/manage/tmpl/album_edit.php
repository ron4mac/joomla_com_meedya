<?php
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

JHtml::stylesheet('components/com_meedya/static/css/manage.css');
JHtml::_('jquery.framework', false);
$jdoc = JFactory::getDocument();
$jdoc->addScript('components/com_meedya/static/js/arrange.js');
$jdoc->addScriptDeclaration('var baseURL = "'.JUri::base().'";
var aBaseURL = "'.JUri::base().'index.php?option=com_meedya&format=raw&mID='.urlencode($this->meedyaID).'&task=";
var albumID = '.$this->aid.';
');
//var_dump($this->album);
?>
<div class="albman">
	<div class="albprp">
		Title:<br />
		<input type="text" name="albttl" value="<?=$this->album['title']?>" /><br />
		Description:<br />
		<textarea name="albdsc"><?=$this->album['desc']?></textarea>
	</div>
	<div class="albthm">
	</div>
</div>
<form>
<div class="display-limit">
	<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
	<?php echo $this->pagination->getLimitBox(); ?>
</div>
</form>
<div class="meedya-gallery">
<?php if ($this->params->def('show_page_heading', 1)) : ?>
<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
</h1>
<?php endif; ?>
<!-- <p>
	<?php var_dump($this->items); ?>
</p> -->
<div>
	<a href="#" title="select all images" onclick="selAllImg(event)">Select All</a>
	<a href="#" title="un-select all images" onclick="selNoImg(event)">Select None</a>
	<a href="javascript:void(0)" title="edit selected images" onclick="editSelected(event)">Edit selected images</a>
	<a href="#" title="remove selected from album" onclick="removeSelected(event)">Remove selected images from album</a>
</div>
<form id="actform" method="POST" action="<?=JRoute::_('index.php?option=com_meedya')?>" style="display:none">
	<input name="task" id="atask" type="hidden" value="manage.imgEdit" />
	<input name="items" id="aitems" type="hidden" value="" />
</form>
<div id="area">
<?php
	foreach ($this->items as $item) {
		if (!$item) continue;
		$thumb = $this->getItemThumb($item);
?><div class="anitem" data-iid="<?=$item?>" onclick="arrangeSel(event,this)"><div><img src="<?= $this->gallpath.'/thm/'.$thumb ?>" /></div></div><?php
	}
?>
<div id="itmend" class="noitem"></div>
</div>
</div>
<div class="page-footer">
	<?php echo $this->pagination->getListFooter(); ?>
</div>
<script>
initArrange();
</script>