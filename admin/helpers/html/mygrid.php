<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
defined('JPATH_PLATFORM') or die;

use Joomla\CMS\HTML\HTMLHelper;

abstract class JHtmlMyGrid
{

	public static function checkall ()
	{
		$html = HTMLHelper::_('grid.checkall');
		return $html;
	}

}