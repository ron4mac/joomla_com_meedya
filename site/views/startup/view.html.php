<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
class MeedyaViewStartup extends JViewLegacy
{
	protected $userPerms = null;

	function display ($tpl = null)
	{
		$this->userPerms = MeedyaHelper::getUserPermissions();
		parent::display($tpl);
	}

}