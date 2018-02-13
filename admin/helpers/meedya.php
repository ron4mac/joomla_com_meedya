<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2017 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

abstract class MeedyaHelper
{
	protected static $instanceType = null;
	protected static $ownerID = null;
	protected static $udp = null;

	public static $ssDefault = array(
			'aA'=>1,	//slideshow action icon at album header
			'aT'=>1,	//shoehorn in this slideshow action at thumbs page
			'uA'=>1,	//user allow album settings (and their default)
			'nW'=>0,	//new (pop) window
			'pS'=>2,	//picture size (intermediate/full)
			'tT'=>'d',	//image transition = dissolve
			'vT'=>1,	//show Title in text area
			'vD'=>1,	//show Desc in title area
			'sI'=>0,	//shuffle slides for show
			'aP'=>1,	//autoplay
			'lS'=>0,	//loop slideshow
			'sD'=>5,	//slide duration
			'dC'=>array('#666','#CCC','rgba(51,51,51,0.5)','#FFF','#000'),	//control background, control text, text background, text text, pic background
			'iS'=>'cb1' //iconset
		);

	public static function scriptVersion ($scr)
	{
		$bg = array(
			'echo'=>'echo.js',
			'slides'=>'slides.js',
			'upload'=>'upload.js',
			'each'=>'each.js'
			);
		$nb = array(
			'echo'=>'echo.min.js',
			'slides'=>'slides.min.js',
			'upload'=>'upload.min.js',
			'each'=>'each.js'
			);
		if (JDEBUG) return $bg[$scr];
		else return $nb[$scr];
	}

	public static function getStorageBase ()
	{
		$dispatcher = JDispatcher::getInstance();
		$results = $dispatcher->trigger('onRjuserDatapath', null);
		$sdp = isset($results[0]) ? trim($results[0]) : '';
		return $sdp ? $sdp : 'userstor';
	}

	public static function userDataPath ()
	{
		if (self::$udp) return self::$udp;
		self::getTypeOwner();
		if (self::$ownerID < 0 && self::$instanceType < 2) return '';	//throw new Exception('ACCESS NOT ALLOWED');
		$cmp = JApplicationHelper::getComponentName();
		switch (self::$instanceType) {
			case 0:
				$ndir = '@'. self::$ownerID;
				break;
			case 1:
				$ndir = '_'. self::$ownerID;
				break;
			case 2:
				$ndir = '_0';
				break;
		}

		$dispatcher = JDispatcher::getInstance();
		$results = $dispatcher->trigger('onRjuserDatapath', null);
		$sdp = isset($results[0]) ? trim($results[0]) : '';
		if (!$sdp) $sdp = 'userstor';

		self::$udp = $sdp.'/'.$ndir.'/'.$cmp;
		return self::$udp;
	}

	public static function getDbPaths ($which, $dbname, $full=false, $cmp='')
	{
		$paths = array();
		if (!$cmp) $cmp = JApplicationHelper::getComponentName();
		switch ($which) {
			case 'u':
				$char1 = '@';
				break;
			case 'g':
				$char1 = '_';
				break;
			default:
				$char1 = '';
				break;
		}
		$dpath = JPATH_SITE.'/'.self::getStorageBase().'/';
		if (is_dir($dpath) && ($dh = opendir($dpath))) {
			while (($file = readdir($dh)) !== false) {
				if ($file[0]==$char1) {
					$ptf = $dpath.$file.'/'.$cmp.'/'.$dbname.'.sql3';
					if (file_exists($ptf))
						if ($full) {
							$paths[$file] = $ptf;
						} else {
							$paths[] = $file;
						}
					$ptf = $dpath.$file.'/'.$cmp.'/'.$dbname.'.db3';
					if (file_exists($ptf))
						if ($full) {
							$paths[$file] = $ptf;
						} else {
							$paths[] = $file;
						}
				}
			}
			closedir($dh);
		}
		return $paths;
	}

	public static function userAuth ($uid)
	{
		self::getTypeOwner();
		$user = JFactory::getUser();
		$uid = $user->get('id');
		$ugrps = $user->get('groups');	//var_dump('ug:',$ugrps);
		switch (self::$instanceType) {
			case 0:
				return $uid == self::$ownerID ? 2 : 0;
				break;
			case 1:
			case 2:
				return in_array(self::$ownerID, $ugrps) ? 2 : 1;
				break;
		}
	}

	public static function getActions ()
	{
		$user = JFactory::getUser();
		$result = new JObject;
		$assetName = 'com_meedya';

		$actions = JAccess::getActions($assetName);

		foreach ($actions as $action) {
			$result->set($action->name,	$user->authorise($action->name, $assetName));
		}

		return $result;
	}

	public static function getInstanceID ()
	{
		if (is_null(self::$instanceType)) self::getTypeOwner();
		return base64_encode(self::$instanceType.':'.self::$ownerID);
	}

	// convert string in form n(K|M|G) to an integer value
	public static function to_bytes ($val)
	{
		$val = trim($val);
		$last = strtolower($val[strlen($val)-1]);
		switch($last) {
			case 'g': $val *= 1024;
			case 'm': $val *= 1024;
			case 'k': $val *= 1024;
		}
		return $val;
	}

	// convert integer value to n(K|M|G) string
	public static function to_KMG ($val=0)
	{
		$sizm = 'K';
		$val = (int)$val;
		if ($val) {
			if (($val % 0x40000000) == 0) {
				$sizm = 'G';
				$val >>= 30;
			} elseif (($val % 0x100000) == 0) {
				$sizm = 'M';
				$val >>= 20;
			} else {
			//	$val >>= 10;
			}
		}
		return $val.$sizm;
	}

	public static function formatBytes ($bytes, $precision=2)
	{
		$units = array('B', 'KB', 'MB', 'GB', 'TB');
		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1); 
		$bytes /= pow(1024, $pow);
		return round($bytes, $precision) . ' ' . $units[$pow];
	}

	private static function getTypeOwner ()
	{
		if (is_null(self::$instanceType)) {
			$app = JFactory::getApplication();
			$id = $app->input->getBase64('mID', false);
			if ($id) {
				$ids = explode(':',base64_decode($id));
				self::$instanceType = $ids[0];
				self::$ownerID = $ids[1];
			} else {
				$params = $app->getParams();
				self::$instanceType = $params->get('instance_type');
				switch (self::$instanceType) {
					case 0:
						self::$ownerID = JFactory::getUser()->get('id');
						if (!self::$ownerID) self::$ownerID = -1;
						break;
					case 1:
						self::$ownerID = $params->get('group_auth');
						break;
					case 2:
						self::$ownerID = $params->get('site_auth');
						break;
				}
			}
		//var_dump(self::$instanceType,self::$ownerID);
		}
	}

}