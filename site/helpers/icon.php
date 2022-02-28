<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

class JHtmlIcon
{
	static function create ($meedyaitem, $params)
	{
		$uri = Factory::getURI();

		$url = Route::_(MeedyaHelperRoute::getFormRoute(0, base64_encode($uri)), false);
		$text = HTMLHelper::_('image','system/new.png', Text::_('JNEW'), NULL, true);
		$button = HTMLHelper::_('link',$url, $text);
		$output = '<span class="hasTip" title="'.Text::_('COM_MEEDYA_FORM_CREATE_MEEDYAITEM').'">'.$button.'</span>';
		return $output;
	}


	static function edit ($meedyaitem, $params, $attribs = [])
	{
		$user = Factory::getUser();
		$uri = Factory::getURI();

		if ($params && $params->get('popup')) {
			return;
		}

		if ($meedyaitem->state < 0) {
			return;
		}

		HTMLHelper::_('behavior.tooltip');
		$url = MeedyaHelperRoute::getFormRoute($meedyaitem->id, base64_encode($uri));
		$icon = $meedyaitem->state ? 'edit.png' : 'edit_unpublished.png';
		$text = HTMLHelper::_('image','system/'.$icon, Text::_('JGLOBAL_EDIT'), NULL, true);

		if ($meedyaitem->state == 0) {
			$overlib = Text::_('JUNPUBLISHED');
		}
		else {
			$overlib = Text::_('JPUBLISHED');
		}

		$date = HTMLHelper::_('date',$meedyaitem->created);
		$author = $meedyaitem->created_by_alias ? $meedyaitem->created_by_alias : $meedyaitem->author;

		$overlib .= '&lt;br /&gt;';
		$overlib .= $date;
		$overlib .= '&lt;br /&gt;';
		$overlib .= htmlspecialchars($author, ENT_COMPAT, 'UTF-8');

		$button = HTMLHelper::_('link',Route::_($url, false), $text);

		$output = '<span class="hasTip" title="'.Text::_('COM_MEEDYA_EDIT').' :: '.$overlib.'">'.$button.'</span>';

		return $output;
	}
}
