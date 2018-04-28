<?php
defined('_JEXEC') or die;

//JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
$jdoc = JFactory::getDocument();
$jdoc->addStyleSheet('components/com_meedya/static/css/gallery.css'.$this->bgt);

//echo'<xmp>';var_dump($this->params);echo'</xmp>';
?>

<div class="meedya-gallery">
<?php if ($this->userPerms->canAdmin) echo JHtml::_('meedya.manageMenu', 1); ?>
<?php echo JHtml::_('meedya.pageHeader', $this->params); ?>
<?php
	foreach ($this->items as $item) {
		$thum = $this->getAlbumThumb($item);
?>
<a href="<?=JRoute::_('index.php?option=com_meedya&view=album&aid='.$item->aid, false) ?>" class="alb-thumb">
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
