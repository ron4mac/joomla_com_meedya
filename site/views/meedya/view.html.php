<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;

include_once JPATH_COMPONENT.'/views/meedyaview.php';

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
//		$app = Factory::getApplication();
		$pathway = $this->app->getPathway();
//		$pathway->addItem('My Added Breadcrumb Link', Route::_(''));
		parent::display($tpl);
	}

}
