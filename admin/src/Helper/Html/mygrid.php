<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2024 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.4.0
*/

namespace RJCreations\Meedya\Administrator\Service\Html;


defined('JPATH_PLATFORM') or die;

use Joomla\CMS\HTML\HTMLHelper;

abstract class MyGrid
{

	public static function checkall ()
	{
		$html = HTMLHelper::_('grid.checkall');
		return $html;
	}

	public static function info ($data)
	{
		if (!is_array($data)) return $data;
		$html = '<dl class="MDY-info">';
		foreach ($data as $k=>$v) {
			switch ($k) {
				case 'size':
					$html .= '<dt>'.'Storage Use:'.'</dt><dd>'.JHtmlNumber::bytes($v, 'auto', 1).'</dd>';
					break;
				case 'items':
					$html .= '<dt>'.'Items:'.'</dt><dd>'.$v.'</dd>';
					break;
				case 'atts':
					$html .= '<dt>'.'Attachments:'.'</dt><dd>'.$v.'</dd>';
					break;
				case 'warn':
					$html .= '<dt>'.'Warning:'.'</dt><dd>'.$v.'</dd>';
			}
		}
		return $html.'</dl>';
	}

}