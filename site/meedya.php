<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2017 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

if (JDEBUG) {
	$cml = array(
		'text_file'=>'com_meedya.log.php',
		'text_entry_format'=>'{DATETIME}			{PRIORITY}			{MESSAGE}'
	);
	JLog::addLogger($cml, JLog::ALL, array('com_meedya'));
}

$controller = JControllerLegacy::getInstance('Meedya');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();