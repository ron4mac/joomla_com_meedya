<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

require_once JPATH_BASE . '/components/com_meedya/views/meedyaview.php';

/**
 * View class for a list all databases for needed updates
 */
class MeedyaViewUpdater extends MeedyaView
{
	protected $relm = 'updater';
}
