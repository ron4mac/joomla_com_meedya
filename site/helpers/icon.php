<?php
/**
 * @version		$Id$
 * @package		Joomla.Site
 * @subpackage	com_meedya
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.helper');
/**
 * MeedyaItem Component HTML Helper
 *
 * @static
 * @package		Joomla.Site
 * @subpackage	com_meedya
 * @since 1.5
 */
class JHtmlIcon
{
	static function create($meedyaitem, $params)
	{
		$uri = JFactory::getURI();

		$url = JRoute::_(MeedyaHelperRoute::getFormRoute(0, base64_encode($uri)));
		$text = JHtml::_('image','system/new.png', JText::_('JNEW'), NULL, true);
		$button = JHtml::_('link',$url, $text);
		$output = '<span class="hasTip" title="'.JText::_('COM_MEEDYA_FORM_CREATE_MEEDYAITEM').'">'.$button.'</span>';
		return $output;
	}

	static function edit($meedyaitem, $params, $attribs = array())
	{
		$user = JFactory::getUser();
		$uri = JFactory::getURI();

		if ($params && $params->get('popup')) {
			return;
		}

		if ($meedyaitem->state < 0) {
			return;
		}

		JHtml::_('behavior.tooltip');
		$url	= MeedyaHelperRoute::getFormRoute($meedyaitem->id, base64_encode($uri));
		$icon	= $meedyaitem->state ? 'edit.png' : 'edit_unpublished.png';
		$text	= JHtml::_('image','system/'.$icon, JText::_('JGLOBAL_EDIT'), NULL, true);

		if ($meedyaitem->state == 0) {
			$overlib = JText::_('JUNPUBLISHED');
		}
		else {
			$overlib = JText::_('JPUBLISHED');
		}

		$date = JHtml::_('date',$meedyaitem->created);
		$author = $meedyaitem->created_by_alias ? $meedyaitem->created_by_alias : $meedyaitem->author;

		$overlib .= '&lt;br /&gt;';
		$overlib .= $date;
		$overlib .= '&lt;br /&gt;';
		$overlib .= htmlspecialchars($author, ENT_COMPAT, 'UTF-8');

		$button = JHtml::_('link',JRoute::_($url), $text);

		$output = '<span class="hasTip" title="'.JText::_('COM_MEEDYA_EDIT').' :: '.$overlib.'">'.$button.'</span>';

		return $output;
	}
}
