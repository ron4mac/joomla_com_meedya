<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

JLoader::register('MeedyaAdminHelper', JPATH_COMPONENT.'/helpers/meedya.php');

// Access check.
if (!Factory::getUser()->authorise('core.manage', 'com_meedya')) {
	return JError::raiseWarning(404, Text::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

$controller = JControllerLegacy::getInstance('Meedya');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
