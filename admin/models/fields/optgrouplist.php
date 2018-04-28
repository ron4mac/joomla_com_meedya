<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2016 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('UserGroupList');

class JFormFieldOptGroupList extends Joomla\CMS\Form\Field\UsergrouplistField
{
	protected $type = 'OptGroupList';

	// Just adds an option so NO usergroup can be selected
	protected function getOptions ()
	{
		$opts = parent::getOptions();
		array_unshift($opts, (object)array('text'=>JText::_('JSELECT'),'value'=>'','level'=>0));
		return $opts;
	}

}
