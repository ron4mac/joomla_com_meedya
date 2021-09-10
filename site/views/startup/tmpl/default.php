<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2021 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Router\Route;

//echo'<xmp>';var_dump($this->params);echo'</xmp>';
?>
<h1><?php echo $this->params->get('page_title'); ?> Startup Screen</h1>
<?php if ($this->userPerms->canAdmin): ?>
<div>
	<p>Here you can start your own media gallery (photos and short videos).</p>
	<p>Max file upload size is <?=$this->maxUpload?>. Total gallery storage is <?=$this->storQuota?>.</p>
	<p>You may select one of your albums to share (along with its sub-albums) in the public space.</p>
</div>
<div>
	<form action="<?=Route::_('index.php?option=com_meedya&Itemid='.$this->itemId, false)?>" method="post">
		<button type="submit" class="btn btn-primary">Start The Gallery</button>
		<input type="hidden" name="task" value="begin" />
	</form>
</div>
<?php else: ?>
<h3>This gallery has not yet been initiated</h3>
<?php endif; ?>
