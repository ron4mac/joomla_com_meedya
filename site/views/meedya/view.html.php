<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.3.2
*/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;

class MeedyaViewMeedya extends MeedyaView
{
	protected $manage = 1;
//	protected $userPerms = null;

	function display ($tpl=null)
	{
	//	$this->manage = Factory::getUser()->authorise('core.edit', 'com_meedya');
//		$this->user = Factory::getUser();
//		$this->userPerms = MeedyaHelper::getUserPermissions();

//		echo'<xmp>';var_dump($this->get('State'), $this->itemId);echo'</xmp>';

		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		foreach ($this->items as &$alb) {
			$alb->isClone = false;
			if ($alb->items && substr($alb->items,0,1)=='*') {
				$alb->isClone = true;
				$alb->oaid = (int) substr($alb->items,1);
			} else {
			//	$alb->items = $alb->items ? count(explode('|',$alb->items)) : 'no';
			}
		}
//		$app = Factory::getApplication();
		$pathway = $this->app->getPathway();
//		$pathway->addItem('My Added Breadcrumb Link', Route::_(''));
		parent::display($tpl);
	}

}
