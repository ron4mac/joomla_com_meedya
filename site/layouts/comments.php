<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2021 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

use Joomla\CMS\HTML\HTMLHelper;

$_BS = ((int)JVERSION < 4) ? '' : '-bs';

echo HTMLHelper::_(
	'bootstrap.renderModal',
	'comments-modal', // selector
	array( // options
		'title'  => 'Comments',
		'footer' => '<button type="button" class="btn btn-secondary" data'.$_BS.'-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary" data'.$_BS.'-target="#comment-modal" data'.$_BS.'-toggle="modal" data'.$_BS.'-dismiss="modal">Add Comment</button>',
	//	'modalWidth' => 30
	),
	'<div class="comments"></div>'
);
