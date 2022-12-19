<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

// Access check.
if (!Factory::getUser()->authorise('core.manage', 'com_meedya')) {
	return JError::raiseWarning(403, Text::_('JERROR_ALERTNOAUTHOR'));
}

// register the library for common user storage actions
JLoader::register('RJUserCom', JPATH_LIBRARIES . '/rjuser/com.php');
// and a general helper
JLoader::register('MeedyaAdminHelper', JPATH_COMPONENT.'/helpers/meedya.php');

// Include dependancies
jimport('joomla.application.component.controller');

$controller = JControllerLegacy::getInstance('Meedya');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
