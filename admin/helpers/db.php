<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2016 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

abstract class MeedyaHelperDb
{
	public static function buildDb ($db)
	{
		$execs = explode(';', file_get_contents(JPATH_COMPONENT_ADMINISTRATOR.'/sql/usernotes.sql'));
		foreach ($execs as $exec) {
			$exec = trim($exec);
			if ($exec[0] != '#') $db->setQuery($exec)->execute();
		}
	}

	public static function rebuildExpodt ($udbPath)
	{
		if (!file_exists($udbPath)) return;
		$imgsDir = $udbPath.'/img/';
		$db = JDatabaseDriver::getInstance(array('driver'=>'sqlite', 'database'=>$udbPath.'/meedya.db3'));

		// get image exif exposure dates and add them to the database
		$db->setQuery('SELECT id,file FROM meedyaitems');
		$files = $db->loadAssocList();
		foreach ($files as $file) {
			$xf = exif_read_data($imgsDir.$file['file'] , 'FILE,COMPUTED,ANY_TAG,IFDO,THUMBNAIL,COMMENT,EXIF', true);
			if (isset($xf['EXIF']['DateTimeOriginal'])) {
				if ($xf['EXIF']['DateTimeOriginal'] != '0000:00:00 00:00:00') $db->setQuery('UPDATE meedyaitems SET expodt="'.$xf['EXIF']['DateTimeOriginal'].'" WHERE id='.$file['id']);
				else $db->setQuery('UPDATE meedyaitems SET expodt=NULL WHERE id='.$file['id']);
				$db->execute();
			}
		}
	}

}