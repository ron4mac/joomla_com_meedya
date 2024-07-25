<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2024 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.4.0
*/
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use RJCreations\Component\Meedya\Site\Helper\HtmlMeedya;
use RJCreations\Component\Meedya\Site\Helper\MeedyaHelper;

MeedyaHelper::oneStyle('a');
?>

<div class="meedya-gallery">
<?php echo HtmlMeedya::pageHeader($this->params); ?>
	<div class="crumbs">
	<?php
		foreach ($this->pathWay as $crm) {
			echo '<span class="crumb"><a href="'.$crm->link.'">'.$crm->name.'</a></span> &gt; ';
		}
		echo '<span class="albttl">'.$this->title.'</span>';
	?>
	</div>
	<div id="albdesc"><?php echo $this->desc; ?></div>
	<div id="area">
	<?php
		$msg = $this->isSearch ? 'COM_MEEDYA_EMPTY_RESULTS' : 'COM_MEEDYA_NO_ITEMS';
		echo '<p style="text-align:center;font-size:large">'.Text::_($msg).'</p>';
	?>
	</div>
</div>
<div class="page-footer">
	<?php echo $this->pagination->getListFooter(); ?>
</div>
