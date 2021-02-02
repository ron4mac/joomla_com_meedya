<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2020 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;

MeedyaHelper::addStyle('gallery');

//echo'<xmp>';var_dump($this->params);echo'</xmp>';
?>

<div class="meedya-gallery">
<?php if ($this->userPerms->canAdmin || $this->userPerms->canUpload) echo JHtml::_('meedya.manageMenu', $this->userPerms, 0, $this->itemId); ?>
<?php echo JHtml::_('meedya.pageHeader', $this->params); ?>
<div class="albthumbs">
<?php
	foreach ($this->items as $item) {
		$thum = $this->getAlbumThumb($item);
?>
<a href="<?=Route::_('index.php?option=com_meedya&view=album&aid='.$item->aid.'&Itemid='.$this->itemId, false) ?>" class="alb-thumb">
	<div><img src="<?=$thum?>" /></div>
	<div class="alb-thm-ttl"><?= $item->title ?></div>
</a>
<?php
	}
?>
</div>
</div>
<div class="page-footer">
	<?php echo $this->pagination->getListFooter(); ?>
</div>
