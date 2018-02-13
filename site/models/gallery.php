<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2017 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

require_once __DIR__ . '/meedya.php';

JLoader::register('MeedyaHelper', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/meedya.php');

class MeedyaModelGallery extends MeedyaModelMeedya
{
	public function __construct ($config = array())
	{
		if (JDEBUG) {
			JLog::add('MeedyaModelGallery', JLog::DEBUG, 'com_meedya');
		}
		parent::__construct($config);
	}

	protected function getListQuery ()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('meedyaitems');
		return $query;
	}

}