<?php
/**
 * @version		$Id: form.php 20899 2011-03-07 20:56:09Z ian $
 * @package		Joomla.Site
 * @subpackage	com_meedya
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT_ADMINISTRATOR.'/models/meedyaitem.php';

/**
 * Meedya model.
 *
 * @package		Joomla.Site
 * @subpackage	com_meedya
 * @since		1.6
 */
no class MeedyaModelForm extends MeedyaModelMeedyaItem
{
	/**
	 * Get the return URL.
	 *
	 * @return	string	The return URL.
	 * @since	1.6
	 */
	public function getReturnPage()
	{
		return base64_encode($this->getState('return_page'));
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication();
		$input = JFactory::getApplication()->input;

		// Load state from the request.
		$pk = $input->getInt('w_id');
		$this->setState('meedyaitem.id', $pk);
		// Add compatibility variable for default naming conventions.
		$this->setState('form.id', $pk);

		$categoryId	= $input->getInt('catid');
		$this->setState('meedyaitem.catid', $categoryId);

		$return = $input->getBase64('return', null);

		if (!JUri::isInternal(base64_decode($return))) {
			$return = null;
		}

		$this->setState('return_page', base64_decode($return));

		// Load the parameters.
		$params	= $app->getParams();
		$this->setState('params', $params);

		$this->setState('layout', $input->getCmd('layout'));
	}
}
