<?php
defined('_JEXEC') or die;

include_once JPATH_COMPONENT.'/views/meedyaview.php';

class MeedyaViewMeedya extends MeedyaView
{
	protected $manage = 1;
	protected $userPerms = null;

	function display ($tpl = null)
	{
	//	$this->manage = JFactory::getUser()->authorise('core.edit', 'com_meedya');
		$this->user = JFactory::getUser();
		$this->userPerms = MeedyaHelper::getUserPermissions();
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$app = JFactory::getApplication();
		$pathway = $app->getPathway();
		$pathway->addItem('My Added Breadcrumb Link', JRoute::_(''));
		parent::display($tpl);
	}

}