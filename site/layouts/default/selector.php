<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

defined('JPATH_BASE') or die;

extract($displayData);	//view,options
?>
<div class="js-stools-field-selector">
	<?php echo $view->filterForm->getField($options['selectorFieldName'])->input; ?>
</div>
