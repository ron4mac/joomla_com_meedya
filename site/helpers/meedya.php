<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2021 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

abstract class MeedyaHelper
{
	protected static $instanceType = null;
	protected static $ownerID = null;
	protected static $udp = null;
	protected static $jdoc = null;

	public static function scriptVersion ($scr, $path='js/')
	{
		$dbg = RJC_DBUG;
		$sfx = $dbg ? ('?'.time()) : '';
		$vray = array(
			'manage' => array('manage.js', 'manage.js'),
			'echo' => array('echo.js', 'echo.min.js'),
			'slides' => array('slides.js', 'slides.min.js'),
			'upload' => array('upload.js', 'upload.min.js'),
			'each' => array('each.js', 'each.js'),
			'basicLightbox' => array('basicLightbox.min.js', 'basicLightbox.min.js')
			);
		if (isset($vray[$scr])) {
			$s = $vray[$scr][$dbg ? 0 : 1];
		} else {
			$s = $scr.'.js';
		}
		return 'components/com_meedya/static/' . $path . $s . $sfx;
	}

	public static function addScript ($scr, $path='js/')
	{
		if (self::$jdoc === null) self::$jdoc = Factory::getDocument();
		self::$jdoc->addScript(self::scriptVersion($scr, $path));
	}

	public static function styleVersion ($css, $path='css/')
	{
		$dbg = RJC_DBUG;
		$sfx = $dbg ? ('?'.time()) : '';
		$vray = array(
			'manage' => array('manage.css', 'manage.css'),
			'echo' => array('echo.css', 'echo.min.css'),
			'slides' => array('slides.css', 'slides.min.css'),
			'upload' => array('upload.css', 'upload.min.css'),
			'each' => array('each.css', 'each.css'),
			'basicLightbox' => array('basicLightbox.min.css', 'basicLightbox.min.css')
			);
		if (isset($vray[$css])) {
			$s = $vray[$css][$dbg ? 0 : 1];
		} else {
			$s = $css.'.css';
		}
		return 'components/com_meedya/static/' . $path . $s . $sfx;
	}

	public static function addStyle ($css, $path='css/')
	{
		if (self::$jdoc === null) self::$jdoc = Factory::getDocument();
		self::$jdoc->addStyleSheet(self::styleVersion($css, $path));
	}

	public static function getInstanceID ()
	{
		if (is_null(self::$instanceType)) self::getTypeOwner();
		return base64_encode(self::$instanceType.':'.self::$ownerID);
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

		$result = Factory::getApplication()->triggerEvent('onRjuserDatapath');
		$sdp = isset($result[0]) ? trim($result[0]) : 'userstor';

		self::$udp = $sdp.'/'.$ndir.'/'.$cmp;
		return self::$udp;
	}

	public static function getUserPermissions ($user, $params)
	{
		static $perms = [];

		if (!$perms) {
	//		echo'<xmp>';var_dump($user->groups,$params);echo'</xmp>';
			$admgrps = $params->get('admin_group', null);
			if (!is_array($admgrps)) $admgrps = [$admgrps];
			if ($params->get('instance_type', 3) > 0) {
				if ($admgrps) {
					$perms['canAdmin'] = !empty(array_intersect($user->groups, $admgrps));
				} else {
					$perms['canAdmin'] = in_array($params->get('owner_group', null), $user->groups);
				}
			} else {
				$perms['canAdmin'] = $user->id > 0;
			}
			if (!$perms['canAdmin']) $perms['canAdmin'] = Factory::getUser()->authorise('core.edit', 'com_meedya');
			$perms['canUpload'] = $perms['canAdmin'] || in_array($params->get('owner_group', null), $user->groups)
								|| array_intersect($params->get('upload_group', []), $user->groups);
		}
		return (object)$perms;
	}

	public static function getGalStruct ($list)
	{
		foreach ($list as &$alb) {
			$alb['items'] = $alb['items'] ? count(explode('|',$alb['items'])) : 'no';
		}
		return $list;
	}

	// return the instance max file upload size
	public static function maxUpload ($op)
	{
		$cupmax = $op ?: self::componentOption('maxUpload', 4194304);
		$cupmax = $cupmax ?: 4194304;
		$cupmax = self::instanceOption('maxUpload', $cupmax);
		return min($cupmax, JFilesystemHelper::fileUploadMaxSize(false));
	}

	// gat a resolved option value based on component and instance (same-named) values
	public static function getResolvedOption ($opt, $dflt=null)
	{
		$optval = self::instanceOption($opt);
		$optval = $optval ?: self::componentOption($opt);
		return $optval ?: $dflt;
	}

	// return the instance storage quota
	public static function getStoreQuota ($prms)
	{
		$isq = $prms->get('storQuota');
		if (!$isq) $isq = self::componentOption('storQuota', 268435456);
		return $isq;
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

	// convert integer value to n(K|M|G) string
	public static function formatBytes ($bytes, $precision=2)
	{
		$units = array('B', 'KB', 'MB', 'GB', 'TB');
		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);
		$bytes /= pow(1024, $pow);
		return round($bytes, $precision) . $units[$pow];
	}

	public static function log ($msg, $data=null)
	{
		if ($msg) JLog::add($msg, JLog::INFO, 'com_meedya');
		if ($data) {
			$msg = '';
			if (!is_array($data)) $data = array($data);
			foreach ($data as $dat) {
				$msg .= print_r($dat, true);
			}
			JLog::add($msg, JLog::DEBUG, 'com_meedya');
		}
	}


// PRIVATE METHODS
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


	private static function instanceOption ($key, $dflt=null)
	{
		static $ip;

		if (empty($ip)) {
			$ip = Factory::getApplication()->getParams();
		//	$active = Factory::getApplication()->getMenu()->getActive();
		//	echo'<xmp>';var_dump($active,$ip);echo'</xmp>';
			if (RJC_DBUG) self::log('inst opts', $ip);
		}

		return $ip->get($key) ?: $dflt;
	}

	private static function componentOption ($key, $dflt=null)
	{
		static $cp;

		if (empty($cp)) {
			$cp = JComponentHelper::getParams('com_meedya');
			if (RJC_DBUG) self::log('comp opts', $cp);
		}

		return $cp->get($key) ?: $dflt;
	}

}
