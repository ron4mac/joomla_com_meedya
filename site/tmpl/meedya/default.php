<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2024 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.4.0
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
MeedyaHelper::oneScript('m');
$jslang = [
		'no_sterm' => Text::_('COM_MEEDYA_MSG_STERM'),
		'ru_sure' => Text::_('COM_USERNOTES_RU_SURE')
	];
$this->jDoc->addScriptDeclaration('Meedya.L = '.json_encode($jslang).';
');

//echo'<xmp>';var_dump($this->params);echo'</xmp>';
?>

<div class="meedya-gallery">
<?php echo \HtmlMeedya::pageHeader($this->params); ?>
<?php if ($this->userPerms->canAdmin || $this->userPerms->canUpload) echo \HtmlMeedya::manageMenu($this->userPerms, 0, $this->itemId); ?>
<?php echo \HtmlMeedya::searchField(0); ?>
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
	<?php echo $this->pagination ? $this->pagination->getListFooter() : ''; ?>
</div>
