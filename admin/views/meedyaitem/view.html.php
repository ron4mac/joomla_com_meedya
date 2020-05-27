<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2020 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View to edit a meedyaitem.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_meedya
 * @since		1.5
 */
class MeedyaViewMeedyaItem extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= MeedyaHelper::getActions($this->state->get('filter.category_id'), $this->item->id);

		JToolBarHelper::title(JText::_('COM_MEEDYA_MANAGER_MEEDYAITEM'), 'meedya.png');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit')||(count($user->getAuthorisedCategories('com_meedya', 'core.create')))))
		{
			JToolBarHelper::apply('meedyaitem.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('meedyaitem.save', 'JTOOLBAR_SAVE');
		}
		if (!$checkedOut && (count($user->getAuthorisedCategories('com_meedya', 'core.create')))){
			JToolBarHelper::custom('meedyaitem.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}
		// If an existing item, can save to a copy.
		if (!$isNew && (count($user->getAuthorisedCategories('com_meedya', 'core.create')) > 0)) {
			JToolBarHelper::custom('meedyaitem.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}
		if (empty($this->item->id)) {
			JToolBarHelper::cancel('meedyaitem.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('meedyaitem.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolBarHelper::divider();
		JToolBarHelper::help('JHELP_COMPONENTS_MEEDYA_LINKS_EDIT');
	}
}
