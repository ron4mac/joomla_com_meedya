<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2016 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_BASE . '/components/com_meedya/helpers/meedya.php';

/**
 * View class for a list of user schedules.
 */
class MeedyaView extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');	//var_dump($this->state);
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		//UserNotesHelper::addSubmenu($this->relm);
		$this->addSubmenu($this->relm);

		// Check for errors.
		//		if (count($errors = $this->get('Errors'))) {
		//			JError::raiseError(500, implode("\n", $errors));
		//			return false;
		//		}

		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}


	/**
	 * Add submenu items
	 */
	protected function addSubmenu ($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_MEEDYA_SUBMENU_USER'),
			'index.php?option=com_meedya',
			$vName == 'user'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_MEEDYA_SUBMENU_GROUP'),
			'index.php?option=com_meedya&view=group',
			$vName == 'group'
		);
//		JHtmlSidebar::addEntry(
//			JText::_('COM_USERNOTES_SUBMENU_SITE'),
//			'index.php?option=com_usernotes&view=site',
//			$vName == 'site'
//		);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		$canDo	= MeedyaHelper::getActions();

		JToolBarHelper::title(JText::_('COM_MEEDYA_MENU').': '.JText::_('COM_MEEDYA_MANAGER_'.strtoupper($this->relm)), 'stack meedya');

		JToolBarHelper::deleteList(JText::_('COM_MEEDYA_MANAGER_DELETEOK'));
		//JToolBarHelper::trash('usernotes.trash');

	//	if ($canDo->get('core.edit.state')) {
	//		JToolBarHelper::custom('notes.reset', 'refresh.png', 'refresh_f2.png', 'JUSERSCHED_RESET', false);
	//	}

		JToolBarHelper::custom('rebuildExpodt', 'wrench', '', 'Rebuild exposure dates');

		JToolBarHelper::divider();
		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_meedya');
		}
		JToolBarHelper::divider();
		JToolBarHelper::help('meedya_manage', true);
	}

	protected function state ($vari, $set=false, $val='', $glb=false)
	{
		$stvar = ($glb?'':'com_meedya.').$vari;
		$app = JFactory::getApplication();
		if ($set) {
			$app->setUserState($stvar, $val);
			return;
		}
		return $app->getUserState($stvar, '');
	}

}
