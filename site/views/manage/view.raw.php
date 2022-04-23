<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class MeedyaViewManage extends JViewLegacy
{

	public function __construct ($config = [])
	{
	//	if (RJC_DBUG) { MeedyaHelper::log('MeedyaViewManageRaw'); }
		parent::__construct($config);
	}

	public function display ($tpl=null)
	{
	//	echo $this->get('Content');
	echo 'tmpl=component& tmpl=component& tmpl=component& tmpl=component&';
	}

}
