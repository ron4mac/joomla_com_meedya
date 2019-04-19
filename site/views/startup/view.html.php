<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class MeedyaViewStartup extends JViewLegacy
{
	protected $user;
	protected $params;
	protected $userPerms = null;

	function display ($tpl=null)
	{
		$this->user = JFactory::getUser();
		$this->params = JFactory::getApplication()->getParams();
		$this->userPerms = MeedyaHelper::getUserPermissions($this->user, $this->param);
		parent::display($tpl);
	}

}
