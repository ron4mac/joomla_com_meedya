<?php
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