<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2023 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

defined('JPATH_BASE') or die;

extract($displayData);	//view,options
?>
<div class="alert alert-info alert-no-items">
	<?php echo $options['noResultsText']; ?>
</div>
