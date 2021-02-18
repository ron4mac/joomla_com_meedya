<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2020 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Router\Route;

//echo'<xmp>';var_dump($this->params);echo'</xmp>';
?>
<h1><?php echo $this->params->get('page_title'); ?> Startup Screen</h1>
<?php if ($this->userPerms->canAdmin): ?>
<div>
	<p>Great!!! So you want to try this out, do you...</p>
	<p>Please be patient and give me a few weeks to figure out what to do here.</p>
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
