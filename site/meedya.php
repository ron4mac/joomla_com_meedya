<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;

define('RJC_DBUG', JDEBUG && file_exists(JPATH_ROOT.'/rjcdev.php'));

// provide a general helper class for the rest of the component
JLoader::register('MeedyaHelper', JPATH_COMPONENT.'/helpers/meedya.php');
// and a J3/J4 compatability helper
JLoader::register('M34C', JPATH_COMPONENT.'/helpers/m34c.php');

if (RJC_DBUG) {
	$cml = [
		'text_file'=>'com_meedya.log.php',
		'text_entry_format'=>'{DATETIME}			{PRIORITY}			{MESSAGE}'
	];
	JLog::addLogger($cml, JLog::ALL, ['com_meedya']);
}
$task = Factory::getApplication()->input->get('task','none');	file_put_contents('TASK.txt',$task."\n",FILE_APPEND);
$controller = BaseController::getInstance('Meedya');
$controller->execute(Factory::getApplication()->input->get('task','none'));
$controller->redirect();
