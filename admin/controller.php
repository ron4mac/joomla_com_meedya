<?php
/**
 * @version		$Id: controller.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Administrator
 * @subpackage	com_meedya
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Meedya MeedyaItem Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	com_meedya
 * @since		3.0
 */
class MeedyaController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			$cachable	If true, the view output will be cached
	 * @param	array			$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display ($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT.'/helpers/meedya.php';

		// Load the submenu.
		MeedyaHelper::addSubmenu($this->input->getCmd('view', 'meedya'));

		$view = $this->input->getCmd('view', 'meedya');
		$layout = $this->input->getCmd('layout', 'default');
		$id = $this->input->getInt('id');

		// Check for edit form.
		if ($view == 'meedyaitem' && $layout == 'edit' && !$this->checkEditId('com_meedya.edit.meedyaitem', $id)) {
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_meedya&view=meedya', false));

			return false;
		}

		parent::display();

		return $this;
	}
}