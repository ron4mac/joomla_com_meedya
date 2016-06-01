<?php
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

JHtml::stylesheet('components/com_meedya/static/css/album.css');
//JHtml::_('behavior.tooltip','.hasTip',array('fixed'=>true));
$jdoc = JFactory::getDocument();

$ttscript = '
    jQuery(document).ready(function() {
        jQuery(\'[data-toggle="tooltip"]\').tooltip();
    });
';

$jdoc->addScriptDeclaration($ttscript);

///<form>
///<div class="display-limit">
///	<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?&#160;
///	<?php echo $this->pagination->getLimitBox(); ?
///</div>
///</form>

//var_dump($this->albums);
?>
<style>
.tooltip.in {
	opacity: 0.85;
	filter: alpha(opacity=85);
}
.tooltip-inner {
	color: #000;
	background-color: #EA9;
	white-space: normal;
}
.tooltip.top .tooltip-arrow {
	border-top-color: #EA9;
}
.tooltip.right .tooltip-arrow {
	border-right-color: #EA9;
}
.tooltip.left .tooltip-arrow {
	border-left-color: #EA9;
}
.tooltip.bottom .tooltip-arrow {
	border-bottom-color: #EA9;
}
</style>
<div class="meedya-gallery">
<?php if ($this->params->def('show_page_heading', 1)) : ?>
	<h1>
		<?php echo $this->escape($this->title); ?>
	</h1>
<?php endif; ?>
	<div class="actions">
		<a href="<?=JRoute::_('index.php?option=com_meedya&view=slides&tmpl=component&aid='.$this->aid) ?>" title="<?=JText::_('COM_MEEDYA_SLIDESHOW')?>">
			<img src="components/com_meedya/static/img/slideshow.png" alt="" />
		</a>
	</div>
<div id="area">
<?php foreach ($this->albums as $alb): ?>
	<div class="anitem falbum">
		<a href="<?=JRoute::_('index.php?option=com_meedya&view=album&aid='.$alb->aid) ?>">
			<div><img src="<?=$this->getAlbumThumb($alb)?>" class="falbumi" /></div>
		</a>
	</div><?php endforeach; ?>
<?php
	foreach ($this->items as $item) {
		if (!$item) continue;
		//$thumb = $this->getItemThumb($item);
		list($thumb, $ititle, $idesc) = $this->getItemThumbPlus($item);
		$ttip = ($ititle && $idesc) ? $ititle.'<br />'.$idesc : $ititle.$idesc;
?><div class="anitem"><a href="<?=JRoute::_('index.php?option=com_meedya&view=item&iid='.$item) ?>" class="itm-thumb"><div data-toggle="tooltip" data-placement="bottom" title="<?=$ttip?>"><img src="<?= $this->gallpath.'/thm/'.$thumb ?>" /></div><div class="itm-thm-ttl"><?= $item ?></div></a></div><?php
	}
?>
<!-- <div id="itmend" class="noitem"></div> -->
</div>
</div>
<div class="page-footer">
	<?php echo $this->pagination->getListFooter(); ?>
</div>
<script>
//initArrange();
</script>