<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2023-2026 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.6.0
*/

defined('JPATH_BASE') or die;

extract($displayData);	//view,options

// Load the form list fields
$list = $view->filterForm->getGroup('list');
?>
<?php if ($list) : ?>
	<div class="ordering-select hidden-phone">
		<?php foreach ($list as $field) : ?>
			<div class="js-stools-field-list">
				<?php echo $field->input; ?>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
