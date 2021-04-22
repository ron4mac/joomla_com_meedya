<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2021 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Event\Dispatcher as EventDispatcher;

abstract class MeedyaAdminHelper
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
		$sfx = JDEBUG ? ('?'.time()) : '';
		$vray = array(
			'echo' => array('echo.js', 'echo.min.js'),
			'slides' => array('slides.js', 'slides.min.js'),
			'upload' => array('upload.js', 'upload.min.js'),
			'each' => array('each.js', 'each.js')
			);
		return $vray[$scr][JDEBUG ? 0 : 1].$sfx;
	}

	public static function getStorageBase ()
	{
		$result = Factory::getApplication()->triggerEvent('onRjuserDatapath');
		$sdp = isset($results[0]) ? trim($results[0]) : '';
		return $sdp ? $sdp : 'userstor';
	}

	public static function getGalStruct ($list)
	{
		foreach ($list as &$alb) {
			$alb['items'] = $alb['items'] ? count(explode('|',$alb['items'])) : 'no';
		}
		return $list;
	}

	public static function sv_userDataPath ()
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

		$result = Factory::getApplication()->triggerEvent('onRjuserDatapath');
		$sdp = isset($result[0]) ? trim($result[0]) : 'userstor';

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
		$user = Factory::getUser();
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

	public static function getGroupTitle ($gid)
	{
		$db = Factory::getDbo();
		$db->setQuery('SELECT title FROM #__usergroups WHERE id='.$gid);
		return ($db->loadResult()?:'- ??? -');
	}

	public static function getActions ()
	{
		$user = Factory::getUser();
		$result = new JObject;
		$assetName = 'com_meedya';

		$actions = JAccess::getActionsFromFile(JPATH_ADMINISTRATOR . '/components/com_meedya/access.xml');
//		$actions = JAccess::getActions($assetName);

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

	public static function getImgProc ($imgf)
	{
	//	if (JDEBUG) { JLog::add('@@ENV@@'.print_r(getenv(), true), JLog::DEBUG, 'com_meedya'); }

		$imp = 'gd';	// default to GD
		if (class_exists('Imagick')) {
			$imp = 'imx';
		} else {
			$sps = explode(':', getenv('PATH'));
			foreach ($sps as $sp) {
				if (file_exists($sp.'/convert')) $imp = 'im';
			}
		}
		require_once JPATH_COMPONENT.'/helpers/graphic'.$imp.'.php';
		return new ImageProcessor($imgf);
	}

	// return the max file upload size as set by the php config
	public static function phpMaxUp ()
	{
		$u = self::to_bytes(ini_get('upload_max_filesize'));
		$p = self::to_bytes(ini_get('post_max_size'));
		return min($p,$u);
	}

	// return the instance storage quota
	public static function getStoreQuota ($prms)
	{
		$isq = $prms->get('storQuota', null);
		if (!$isq) $isq = self::componentOption('storQuota', 268435456);
		return $isq;
	}

	// convert string in form n(K|M|G) to an integer value
	public static function to_bytes ($val)
	{
		$val = trim($val);
		$last = strtolower($val[strlen($val)-1]);
		$val = (int)$val;
		switch($last) {
			case 't': $val *= 1024;
			case 'g': $val *= 1024;
			case 'm': $val *= 1024;
			case 'k': $val *= 1024;
		}
		return $val;
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
			$app = Factory::getApplication();
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
						self::$ownerID = Factory::getUser()->get('id');
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

	private static function componentOption ($key, $dflt)
	{
		static $co;

		if (empty($co)) {
			$co = JComponentHelper::getParams('com_meedya');
		}

		return $co->get($key, $dflt);
	}

}
