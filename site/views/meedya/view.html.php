<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2019 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

include_once JPATH_COMPONENT.'/views/meedyaview.php';

class MeedyaViewMeedya extends MeedyaView
{
	protected $manage = 1;
//	protected $userPerms = null;

	function display ($tpl=null)
	{
	//	$this->manage = JFactory::getUser()->authorise('core.edit', 'com_meedya');
//		$this->user = JFactory::getUser();
//		$this->userPerms = MeedyaHelper::getUserPermissions();

//		echo'<xmp>';var_dump($this->get('State'), $this->itemId);echo'</xmp>';

		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$app = JFactory::getApplication();
		$pathway = $app->getPathway();
		$pathway->addItem('My Added Breadcrumb Link', JRoute::_(''));
		parent::display($tpl);
	}

}
