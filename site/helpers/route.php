<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

abstract class MeedyaHelperRoute
{
	protected static $lookup;

	/**
	 * @param	int	The route of the meedyaitem
	 */
	public static function getMeedyaItemRoute($id, $catid)
	{
		$needles = ['meedyaitem'  => [(int) $id]];

		//Create the link
		$link = 'index.php?option=com_meedya&view=meedyaitem&id='. $id;
		if ($catid > 1) {
			$categories = JCategories::getInstance('Meedya');
			$category = $categories->get($catid);

			if($category) {
				$needles['category'] = array_reverse($category->getPath());
				$needles['categories'] = $needles['category'];
				$link .= '&catid='.$catid;
			}
		}

		if ($item = self::_findItem($needles)) {
			$link .= '&Itemid='.$item;
		}
		else if ($item = self::_findItem()) {
			$link .= '&Itemid='.$item;
		}

		return $link;
	}

	/**
	 * @param	int		$id		The id of the meedyaitem.
	 * @param	string	$return	The return page variable.
	 */
	public static function getFormRoute($id, $return = null)
	{
		// Create the link.
		if ($id) {
			$link = 'index.php?option=com_meedya&task=meedyaitem.edit&w_id='. $id;
		}
		else {
			$link = 'index.php?option=com_meedya&task=meedyaitem.add&w_id=0';
		}

		if ($return) {
			$link .= '&return='.$return;
		}

		return $link;
	}

	public static function getCategoryRoute($catid)
	{
		if ($catid instanceof JCategoryNode) {
			$id = $catid->id;
			$category = $catid;
		}
		else {
			$id = (int) $catid;
			$category = JCategories::getInstance('Meedya')->get($id);
		}

		if ($id < 1) {
			$link = '';
		}
		else {
			$needles = ['category' => [$id]];

			if ($item = self::_findItem($needles)) {
				$link = 'index.php?Itemid='.$item;
			}
			else {
				//Create the link
				$link = 'index.php?option=com_meedya&view=category&id='.$id;

				if ($category) {
					$catids = array_reverse($category->getPath());
					$needles = [
						'category' => $catids,
						'categories' => $catids
					];

					if ($item = self::_findItem($needles)) {
						$link .= '&Itemid='.$item;
					}
					else if ($item = self::_findItem()) {
						$link .= '&Itemid='.$item;
					}
				}
			}
		}

		return $link;
	}

	protected static function _findItem($needles = null)
	{
		$app = Factory::getApplication();
		$menus = $app->getMenu('site');

		// Prepare the reverse lookup array.
		if (self::$lookup === null) {
			self::$lookup = [];

			$component	= ComponentHelper::getComponent('com_meedya');
			$items		= $menus->getItems('component_id', $component->id);
			foreach ($items as $item)
			{
				if (isset($item->query) && isset($item->query['view'])) {
					$view = $item->query['view'];

					if (!isset(self::$lookup[$view])) {
						self::$lookup[$view] = [];
					}

					if (isset($item->query['id'])) {
						self::$lookup[$view][$item->query['id']] = $item->id;
					}
				}
			}
		}

		if ($needles) {
			foreach ($needles as $view => $ids)
			{
				if (isset(self::$lookup[$view])) {
					foreach($ids as $id)
					{
						if (isset(self::$lookup[$view][(int)$id])) {
							return self::$lookup[$view][(int)$id];
						}
					}
				}
			}
		}
		else {
			$active = $menus->getActive();
			if ($active) {
				return $active->id;
			}
		}

		return null;
	}
}
