<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2024 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.4.0
*/
namespace RJCreations\Component\Meedya\Site\Helper;

defined('_JEXEC') or die;

// a helper class that was to accommodate Joomla 3 and Joomla 4 differences
// now just returns J4+ items
abstract class M34C
{

	// return the appropriate bootstrap data attribute
	public static function bs ($tag)
	{
		$bs4 = ['dismiss','html','placement','target','toggle'];
		return (in_array($tag, $bs4) ? 'data-bs-' : 'data-') . $tag;
	}

	public static function btn ($which)
	{
		$btns = [
			'p' => 'btn btn-primary',
			's' => 'btn btn-secondary',
			'ps' => 'btn btn-primary btn-sm',
			'ss' => 'btn btn-secondary btn-sm',
			'pl' => 'btn btn-primary btn-lg',
			'sl' => 'btn btn-secondary btn-lg'
		];

		return $btns[$which];
	}

}
