<?php
defined('_JEXEC') or die;

//JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

JHtml::stylesheet('components/com_meedya/static/css/gallery.css');
?>
<div class="meedya-gallery">
<?php if ($this->params->def('show_page_heading', 1)) : ?>
<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
</h1>
<?php endif; ?>
<?php if ($this->manage) echo JHtml::_('meedya.manageMenu', 1); ?>
<?php
	foreach ($this->items as $item) {
		$thum = $this->getAlbumThumb($item);
?>
<a href="<?=JRoute::_('index.php?option=com_meedya&view=album&aid='.$item->aid) ?>" class="alb-thumb">
	<div><img src="<?=$thum?>" width="120px" height="120px" /></div>
	<div class="alb-thm-ttl"><?= $item->title ?></div>
</a>
<?php
	}
?>
</div>
<div class="page-footer">
	<?php echo $this->pagination->getListFooter(); ?>
</div>
