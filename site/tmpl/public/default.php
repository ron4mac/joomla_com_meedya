<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2024 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.3.4
*/
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('jquery.framework');
//MeedyaHelper::addStyle('gallery');
MeedyaHelper::oneStyle('g');
HTMLHelper::_('behavior.core');		// must force 'core' to load before 'meedya' on joomla 3.x
//MeedyaHelper::addScript(['common','meedya']);
MeedyaHelper::oneScript('cme');
$jslang = [
		'no_sterm' => Text::_('COM_MEEDYA_MSG_STERM'),
		'ru_sure' => Text::_('COM_MEEDYA_RU_SURE')
	];
$this->jDoc->addScriptDeclaration('Meedya.L = '.json_encode($jslang).';
');

//echo'<xmp>';var_dump($this->params);echo'</xmp>';
?>

<div class="meedya-gallery">
<?php echo HtmlMeedya::pageHeader($this->params); ?>
<?php // echo HtmlMeedya::searchField(0); ?>
<div class="albthumbs">
<?php
//	echo'<xmp>';var_dump($this->items);echo'</xmp>';
	foreach ($this->items as $item) {
		$pgid = basename(dirname($item->path));
		$pgid .= '|'.substr($item->path, strrpos($item->path, '_'));
		$pgid .= '|'.$item->aid;
		$pgid = base64_encode($pgid);
		$thum = $this->getAlbumThumb($item);
?>
<a href="<?=Route::_('index.php?option=com_meedya&view=public&layout=album&pgid='.$pgid.'&Itemid='.$this->itemId, false) ?>" class="alb-thumb">
	<div class="pubalb"><img src="<?=$thum?>" /><span><?= $item->title ?></span></div>
	<div class="alb-thm-ttl"><?= $item->owner ?></div>
</a>
<?php
	}
?>
</div>
</div>
<div class="page-footer">
	<?php echo $this->pagination->getListFooter(); ?>
</div>
