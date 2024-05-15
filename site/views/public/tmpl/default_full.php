<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2023 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.3.2
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
<?php echo HTMLHelper::_('meedya.pageHeader', $this->params); ?>
<?php // echo HTMLHelper::_('meedya.searchField', 0); ?>
<div class="albthumbs">
<?php
	echo'<xmp>';var_dump($this->items);echo'</xmp>';
	foreach ($this->items as $item) {
		$pgid = basename(dirname($item->path));
		$pgid .= '|'.substr($item->path, strrpos($item->path, '_'));
		$pgid .= '|0';	var_dump($pgid);
		$pgid = base64_encode($pgid);
		echo '<a href="' . Route::_('index.php?option=com_meedya&view=public&pgid='.$pgid.'&Itemid='.$this->itemId, false) . '" class="alb-thumb"><div class="pubfull"><div>'.$item->owner.'</div></div></a>';
	}
?>
</div>
</div>
<div class="page-footer">
	<?php echo $this->pagination->getListFooter(); ?>
</div>
