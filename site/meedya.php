<?php
/**
 * @version		$Id: meedya.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	com_meedya
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

$controller	= JControllerLegacy::getInstance('Meedya');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();