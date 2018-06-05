<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2018 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

MeedyaHelper::addStyle('gallery');

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
