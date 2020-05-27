<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2020 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

JLoader::register('MeedyaAdminHelper', JPATH_COMPONENT.'/helpers/meedya.php');

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_meedya')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

$controller = JControllerLegacy::getInstance('Meedya');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
