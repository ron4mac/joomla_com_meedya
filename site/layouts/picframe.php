<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2023 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
defined('JPATH_BASE') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;

extract($displayData);	//albttl

$mmdl =  HTMLHelper::_(
	'bootstrap.renderModal',
	'picframe-modal', // selector
	array( // options
		'title'  => Text::_('COM_MEEDYA_PICFRAME_TITLE'),
		'footer' => '<button type="button" class="btn btn-secondary" '.M34C::bs('dismiss').'="modal">Close</button>
					<button type="button" class="btn btn-primary" onclick="Meedya.submitPlaylist(this)">Submit to Photo Frame</button>',
		//'modalWidth' => 30
	),
	'<form action="picframe.local" name="picframe" id="picframe" method="POST">
	<input type="text" name="title" id="pl-title" size="30" value="'.$albttl.'"> <label for="pl-title">Playlist Title</label><br>
	<input type="checkbox" name="recur" id="pl-recur" value="1"> <label for="pl-recur">Get images recursively</label><br>
	<label for="pl-vtim">Image view time (secs)</label> <input type="number" name="vtim" id="pl-vtim" value="60"><br>
	<input type="hidden" name="pfkey" value="" />
	<input type="hidden" name="task" value="addList" />
	<input type="hidden" name="'.Session::getFormToken().'" value="1" />
	</form>'
);
//remove the large modal css designation
echo str_replace(' modal-lg', '', $mmdl);
