<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2021 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

abstract class MeedyaHelperDb
{
	public static function checkDbVersion ($udbPath)
	{
		return file_exists(dirname($udbPath).'/.dbver');
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

	public static function cleanOrphans ($udbPath)
	{
		if (!file_exists($udbPath)) return;
	//	$imgFils = self::storedFiles($udbPath.'/img/');
	//	$medFils = self::storedFiles($udbPath.'/med/');
	//	$thmFils = self::storedFiles($udbPath.'/thm/');

		$db = JDatabaseDriver::getInstance(array('driver'=>'sqlite', 'database'=>$udbPath.'/meedya.db3'));

		// get files listed in the database
		$db->setQuery('SELECT file FROM meedyaitems');
		$files = $db->loadColumn();

		$imgFils = self::storedFiles($udbPath.'/img/', $files);
		$medFils = self::storedFiles($udbPath.'/med/', $files);
		$thmFils = self::storedFiles($udbPath.'/thm/', $files);

		$dcnt = 0;
	//	$imgFils = array_diff($imgFils, $files);
//		foreach
	//	$medFils = array_diff($medFils, $files);
	//	$thmFils = array_diff($thmFils, $files);

		echo'<xmp>';var_dump($files,$imgFils,$medFils,$thmFils);echo'</xmp>';jexit();
	}

	public static function recalcStorage ($udbPath)
	{
		if (!file_exists($udbPath)) return;

		$db = JDatabaseDriver::getInstance(array('driver'=>'sqlite', 'database'=>$udbPath.'/meedya.db3'));

		// get files listed in the database
		$db->setQuery('SELECT id,file FROM meedyaitems');
		$files = $db->loadAssocList();

	//	echo'<pre>';
		foreach ($files as $file) {
			$fs = @filesize($udbPath.'/img/'.$file['file']);
			$ms = @filesize($udbPath.'/med/'.$file['file']);
			$ts = @filesize($udbPath.'/thm/'.$file['file']);
			$sz = $fs+$ms+$ts;
		//	echo $sz."\n";
			$db->setQuery('UPDATE meedyaitems SET tsize='.$sz.' WHERE id='.$file['id']);
			$db->execute();
		}
	//	echo'</pre>';jexit();
	}

	public static function updateDatabase ($udbPath)
	{
//	file_put_contents('UPDS.txt', print_r($udbPath, true));
		if (!file_exists($udbPath)) return;

		$curver = file_exists($udbPath.'/.dbver') ? trim(file_get_contents($udbPath.'/.dbver')) : '0.0.0';

		$updsqlfiles = glob(JPATH_COMPONENT_ADMINISTRATOR.'/tables/upd_*.sql', GLOB_NOSORT);
		if (!$updsqlfiles) return;

		natsort($updsqlfiles);
//	file_put_contents('UPDS.txt', print_r($updsqlfiles, true));
		$dbfile = $udbPath.'/meedya.db3';
		if (!file_exists($dbfile)) throw new Exception('COM_MEEDYA_MISSING_DB');
		$db = JDatabaseDriver::getInstance(array('driver'=>'sqlite', 'database'=>$dbfile));
		$errs = [];
		foreach ($updsqlfiles as $sqlf) {
			preg_match('#upd_(.+)\.sql#', basename($sqlf), $m);
			$updver = $m[1];
			if (version_compare($updver, $curver, '<=')) continue;
			$sqls = explode("\n",file_get_contents($sqlf));
			foreach ($sqls as $sql) {
				try {
					if ($sql && $sql[0] != '#') $db->setQuery($sql)->execute();
				} catch (Exception $e) {
					$errs[basename($sqlf)] = $e->getMessage();
					break;
				}
			}
			if ($errs) {
				$msg = $udbPath . ' :: ' . print_r($errs, true).$curver.$updver;
				Factory::getApplication()->enqueueMessage($msg, 'error');
				break;
			} else {
				file_put_contents($udbPath.'/.dbver', $updver);
				$curver = $updver;
			}
		}
		return count($errs);
	}

	public static function fixItemAlbums ($udbPath)
	{
		if (!file_exists($udbPath)) return;

		$db = JDatabaseDriver::getInstance(array('driver'=>'sqlite', 'database'=>$udbPath.'/meedya.db3'));

		// get files listed in the database
		$db->setQuery('SELECT id,album FROM meedyaitems');
		$itms = $db->loadAssocList();

		foreach ($itms as $itm) {
			$albs = explode('|', $itm['album']);
			$vals = []; 
			foreach ($albs as $k=>$v) {    
				$vals[$v] = true; 
			} 
			$albs = array_keys($vals); 
			$db->setQuery('UPDATE meedyaitems SET album=\''.implode('|',$albs).'\' WHERE id='.$itm['id']);
			$db->execute();
		}
	}

	private static function storedFiles ($dir, &$indb)
	{
		$files = [];
		if ($h = opendir($dir)) {
			while (false !== ($entry = readdir($h))) {
				if (($entry[0] != '.') && ($entry != 'index.html')) {
					$files[] = $entry;
				}
			}
			closedir($h);
		} else echo 'CRAP!   ';

		$orfs = array_diff($files, $indb);
		foreach ($orfs as $orf) {
		//	unlink($dir.$orf);
		}

		return $files;
	}

}
