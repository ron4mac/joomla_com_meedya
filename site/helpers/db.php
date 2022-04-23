<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

abstract class MeedyaHelperDb
{
	public static function buildDb ($db)
	{
		$execs = explode(';', file_get_contents(JPATH_COMPONENT_ADMINISTRATOR.'/tables/db3.sql'));
		foreach ($execs as $exec) {
			$exec = trim($exec);
			if ($exec && $exec[0] != '#') $db->setQuery($exec)->execute();
		}
	}
}
