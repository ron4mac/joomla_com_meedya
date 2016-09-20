<?php
defined('_JEXEC') or die;

abstract class MeedyaHelperDb
{
	public static function buildDb ($db)
	{
		$execs = explode(';', file_get_contents(JPATH_COMPONENT_ADMINISTRATOR.'/tables/db3.sql'));
		foreach ($execs as $exec) {
			$db->setQuery($exec);
			$db->execute();
		}
	}
}
