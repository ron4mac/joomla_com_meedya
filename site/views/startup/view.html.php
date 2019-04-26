<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2019 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

class MeedyaViewStartup extends JViewLegacy
{
	protected $user;
	protected $params;
	protected $userPerms = null;

	function display ($tpl=null)
	{
		$this->user = JFactory::getUser();
		$this->params = JFactory::getApplication()->getParams();
		$this->userPerms = MeedyaHelper::getUserPermissions($this->user, $this->params);
		parent::display($tpl);
	}

}
