<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2023 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

define('RJC_DBUG', (true || JDEBUG) && file_exists(JPATH_ROOT.'/rjcdev.php'));

// register base MVC elements
JLoader::register('MeedyaModelMeedya', JPATH_COMPONENT.'/models/meedya.php');
JLoader::register('MeedyaView', JPATH_COMPONENT.'/views/meedyaview.php');

// register the library for common user storage actions
JLoader::register('RJUserCom', JPATH_LIBRARIES . '/rjuser/com.php');

// provide a general helper class for the rest of the component
JLoader::register('MeedyaHelper', JPATH_COMPONENT.'/helpers/meedya.php');
// and a J3/J4 compatability helper
JLoader::register('M34C', JPATH_COMPONENT.'/helpers/m34c.php');

if (RJC_DBUG) {
	$cml = ['text_file'=>'com_meedya.log.php', 'text_entry_format'=>'{DATETIME}			{PRIORITY}			{MESSAGE}'];
	JLog::addLogger($cml, JLog::ALL, ['com_meedya']);
}

$task = Factory::getApplication()->input->get('task','none');
$controller = BaseController::getInstance('Meedya');
$controller->execute(Factory::getApplication()->input->get('task','none'));
$controller->redirect();
