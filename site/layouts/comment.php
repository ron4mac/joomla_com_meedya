<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2021 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

use Joomla\CMS\HTML\HTMLHelper;

echo HTMLHelper::_(
	'bootstrap.renderModal',
	'comment-modal', // selector
	array( // options
		'title'  => 'New Comment',
		'footer' => '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary" onclick="Meedya.submitComment(this)">Submit Comment</button>',
	//	'modalWidth' => 30
	),
	'<form id="newcmnt">
	<div class="new-comment"><textarea id="cmnt-text" name="cmntext"></textarea></div>
	<input type="hidden" name="task" value="addComment" />
	</form>'
);
