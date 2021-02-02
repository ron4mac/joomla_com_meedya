<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2020 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class MeedyaViewStartup extends JViewLegacy
{
	protected $user;
	protected $params;
	protected $userPerms = null;

	function display ($tpl=null)
	{
		$this->user = Factory::getUser();
		$this->params = Factory::getApplication()->getParams();
		$this->userPerms = MeedyaHelper::getUserPermissions($this->user, $this->params);
		parent::display($tpl);
	}

}
