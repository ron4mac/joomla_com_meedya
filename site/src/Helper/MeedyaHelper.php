<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2023-2026 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.5.5
*/
namespace RJCreations\Component\Meedya\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Component\ComponentHelper;
use RJCreations\Library\RJUserCom;

abstract class MeedyaHelper
{
	protected static $instanceObj = null;
	protected static $instanceType = null;
	protected static $ownerID = null;
	protected static $udp = null;
	protected static $jdoc = null;

	public static function oneScript ($str)
	{
		$c2js = [
			'c' => 'com_meedya.common',
			'm' => 'com_meedya.meedya',
			'M' => 'com_meedya.manage',
			'f' => 'vendor/fancybox/3.5.7/jquery.fancybox',
			// NOTE: the v4 fancybox code wrapper has to be removed for it to work here
			'F' => 'vendor/fancybox/4.0.27/fancybox.umd',
			'r' => 'com_meedya.rating',
			'b' => 'com_meedya.my_bb',
			'a' => 'com_meedya.itm_dand',
			'A' => 'com_meedya.alb_dand',
			'u' => 'com_meedya.fileup',
			'U' => 'com_meedya.uplodr',
			'p' => 'com_meedya.pell',
			't' => 'com_meedya.tags',
			'e' => 'com_meedya.echo',	//(lazy load)
			's' => 'js/slides'
		];
		if (self::$jdoc === null) self::$jdoc = Factory::getDocument();
		$wa = self::$jdoc->getWebAssetManager();
		$codes = str_split('c'.$str);
		foreach ($codes as $c) {
			$wa->useScript($c2js[$c]);
		}
//		$s = (RJC_DBUG && Factory::getUser()->get('id') ? 'Dc' : 'c') . $str;
//		self::$jdoc->addScript('components/com_meedya/js.php?c='.$s);
	}

	public static function oneStyle ($str)
	{
		$c2css = [
			'g' => 'com_meedya.css.gallery',
			'a' => 'com_meedya.css.album',
			'm' => 'css/meedya',
			'M' => 'com_meedya.css.manage',
			'f' => 'vendor/fancybox/3.5.7/jquery.fancybox',
			'F' => 'vendor/fancybox/4.0.27/fancybox',
			'r' => 'css/rating',
			'U' => 'com_meedya.css.uplodr',
			'p' => 'com_meedya.css.pell',
			't' => 'com_meedya.css.tags',
			's' => 'css/slides'
		];
		if (self::$jdoc === null) self::$jdoc = Factory::getDocument();
		$wa = self::$jdoc->getWebAssetManager();
		$codes = str_split($str);
		foreach ($codes as $c) {
			$wa->useStyle($c2css[$c]);
		}
	}

	public static function getUserPermissions ($user, $params)
	{
		static $perms = [];

		if (!$perms) {
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
			$alb['isClone'] = false;
			if (substr($alb['items']?:' ',0,1)=='*') {
				$alb['isClone'] = true;
				$alb['oaid'] = (int) substr($alb['items'],1);
			} else {
				$alb['items'] = $alb['items'] ? count(explode('|',$alb['items'])) : 'no';
			}
		}
		return $list;
	}

	// return the instance max file upload size
	public static function maxUpload ($op)
	{
		$cupmax = $op ?: self::componentOption('maxUpload', 4194304);
		$cupmax = $cupmax ?: 4194304;
		$cupmax = self::instanceOption('maxUpload', $cupmax);
		// using my chunking uploader so no need to account for PHP max
		return $cupmax;
	}

	// get a resolved option value based on component and instance (same-named) values
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

	public static function encodeKey ($parms)
	{
		require_once JPATH_COMPONENT.'/classes/crypt.php';
		$key = \ComMeedya\Encryption::simpleXor($parms, Factory::getApplication()->get('secret'));
		return base64_encode($key);
	}

	public static function decodeKey ($key)
	{
		require_once JPATH_COMPONENT.'/classes/crypt.php';
		$secret = Factory::getApplication()->get('secret');
		if (strlen($key)>99) {
			$prms = \ComMeedya\Encryption::decrypt($key, $secret);
		} else {
			$prms = \ComMeedya\Encryption::simpleXor($key, $secret);
		}
		return $prms;
	}

	public static function getImgProc ($imgf)
	{
		$imp = self::componentOption('image_proc');
		if (!$imp) {
			$imp = 'gd';	// default to GD
			if (class_exists('Imagick')) {
				$imp = 'imx';
			} else {
				$sps = explode(':', getenv('PATH'));
				foreach ($sps as $sp) {
					if (file_exists($sp.'/convert')) $imp = 'im';
					if (file_exists($sp.'/magick')) $imp = 'im7';
				}
			}
		}
		require_once JPATH_COMPONENT.'/classes/graphic'.$imp.'.php';
		return new \ImageProcessor($imgf);
	}

	public static $ssDefault = [
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
			'dC'=>['#666','#CCC','rgba(51,51,51,0.5)','#FFF','#000'],	//control background, control text, text background, text text, pic background
			'iS'=>'cb1' //iconset
		];

	// return the max file upload size as set by the php config
	public static function phpMaxUp ()
	{
		$u = self::to_bytes(ini_get('upload_max_filesize'));
		$p = self::to_bytes(ini_get('post_max_size'));
		return min($p,$u);
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

	// convert integer value to n(K|M|G) string
	public static function formatBytes ($bytes, $precision=2)
	{
		$units = ['B','KB','MB','GB','TB'];
		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);
		$bytes /= pow(1024, $pow);
		return round($bytes, $precision) . $units[$pow];
	}

	public static function log ($msg, $data=null)
	{
		if ($msg) Log::add($msg, Log::INFO, 'com_meedya');
		if ($data) {
			$msg = '';
			if (!is_array($data)) $data = [$data];
			foreach ($data as $wh=>$dat) {
				$msg .= "\n".$wh.': '.print_r($dat, true);
			}
			Log::add($msg, Log::DEBUG, 'com_meedya');
		}
	}


// PRIVATE METHODS
	private static function xxgetTypeOwner ()
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
						self::$ownerID = $params->get('owner_group');
						break;
					case 2:
						self::$ownerID = $params->get('site_auth');
						break;
				}
			}
		}
	}


	private static function instanceOption ($key, $dflt=null)
	{
		static $ip;

		if (empty($ip)) {
			$ip = Factory::getApplication()->getParams();
			if (RJC_DBUG) self::log('inst opts', $ip);
		}

		return $ip->get($key) ?: $dflt;
	}

	private static function componentOption ($key, $dflt=null)
	{
		static $cp;

		if (empty($cp)) {
			$cp = ComponentHelper::getParams('com_meedya');
			if (RJC_DBUG) self::log('comp opts', $cp);
		}

		return $cp->get($key) ?: $dflt;
	}

}
