<?php
/**
 * @package    com_meedya
 * @copyright  Copyright (C) 2016-2019 RJCreations - All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

require_once JPATH_BASE . '/components/com_meedya/views/meedyaview.php';

/**
 * View class for a list of user galleries.
 */
class UsersViewMeedya extends MeedyaView
{
	protected $relm = 'user';
}
