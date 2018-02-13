<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2017 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
?>
<div class="meedya-gallery">
<?php if ($this->params->def('show_page_heading', 1)) : ?>
<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
</h1>
<?php endif; ?>
<!-- <p>
	<?php var_dump($this->items); ?>
</p> -->
<?php
	foreach ($this->items as $item) {
		echo '<a href="'.$this->gallpath.'/img/'.$item->file.'"><img src="'.$this->gallpath.'/thm/'.$item->file.'" /></a>';
	}
	//JUri::base().
?>
</div>
<div class="page-footer">
	<?php echo $this->pagination->getListFooter(); ?>
</div>
