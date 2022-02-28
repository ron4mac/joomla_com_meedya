<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
defined('JPATH_BASE') or die;

FormHelper::loadFieldClass('UsergrouplistField');

class OptgrouplistField extends UsergrouplistField
{
	protected $type = 'OptUserGroupList';


	protected function getOptions()
	{
		$opts = parent::getOptions();
		array_unshift($opts, (object)array('text'=>'JSELECT','value'=>'','level'=>0));
		return $opts;
		// Hash for caching
		$hash = md5($this->element);

		if (!isset(static::$options[$hash]))
		{
			static::$options[$hash] = parent::getOptions();

			$groups         = UserGroupsHelper::getInstance()->getAll();
			$checkSuperUser = (int) $this->getAttribute('checksuperusergroup', 0);
			$isSuperUser    = Factory::getUser()->authorise('core.admin');
			$options        = array();

			foreach ($groups as $group)
			{
				// Don't show super user groups to non super users.
				if ($checkSuperUser && !$isSuperUser && Access::checkGroup($group->id, 'core.admin'))
				{
					continue;
				}

				$options[] = (object) array(
					'text'  => str_repeat('- ', $group->level) . $group->title,
					'value' => $group->id,
					'level' => $group->level
				);
			}

			static::$options[$hash] = array_merge(static::$options[$hash], $options);
		}

		return static::$options[$hash];
	}
}
