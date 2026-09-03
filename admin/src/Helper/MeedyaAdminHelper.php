<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2023-2026 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.6.0
*/
namespace RJCreations\Component\Meedya\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Access\Access;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Event\Dispatcher as EventDispatcher;

abstract class MeedyaAdminHelper
{
	protected static $instanceType;
	protected static $siteMenu;
	protected static $ownerID;
	protected static $udp;

	public static $ssDefault = [
			'aA' => 1,	//slideshow action icon at album header
			'aT' => 1,	//shoehorn in this slideshow action at thumbs page
			'uA' => 1,	//user allow album settings (and their default)
			'nW' => 0,	//new (pop) window
			'pS' => 2,	//picture size (intermediate/full)
			'tT' => 'd',	//image transition = dissolve
			'vT' => 1,	//show Title in text area
			'vD' => 1,	//show Desc in title area
			'sI' => 0,	//shuffle slides for show
			'aP' => 1,	//autoplay
			'lS' => 0,	//loop slideshow
			'sD' => 5,	//slide duration
			'dC' => ['#666','#CCC','rgba(51,51,51,0.5)','#FFF','#000'],	//control background, control text, text background, text text, pic background
			'iS' => 'cb1' //iconset
		];

	public static function scriptVersion (string $scr)
	{
		$sfx = JDEBUG ? ('?'.time()) : '';
		$vray = [
			'echo' => ['echo.js', 'echo.min.js'],
			'slides' => ['slides.js', 'slides.min.js'],
			'upload' => ['upload.js', 'upload.min.js'],
			'each' => ['each.js', 'each.js']
			];
		return $vray[$scr][JDEBUG ? 0 : 1].$sfx;
	}

	public static function getGalStruct ($list)
	{
		foreach ($list as &$alb) {
			$alb['items'] = $alb['items'] ? count(explode('|',$alb['items'])) : 'no';
		}
		return $list;
	}

	public static function userAuth ($uid)
	{
		self::getTypeOwner();
		$user = Factory::getApplication()->getIdentity();
		$uid = $user->get('id');
		$ugrps = $user->get('groups');
		return match (self::$instanceType) {
			0 => $uid == self::$ownerID ? 2 : 0,
			1, 2 => in_array(self::$ownerID, $ugrps) ? 2 : 1,
			default => null
		};
	}

	public static function getGroupTitle ($gid)
	{
		$db = Factory::getContainer()->get(DatabaseInterface::class);
		$db->setQuery('SELECT title FROM #__usergroups WHERE id='.$gid);
		return ($db->loadResult()?:'- ??? -');
	}

	public static function getActions ()
	{
		$user = Factory::getApplication()->getIdentity();
		$result = new \stdClass();
		$assetName = 'com_meedya';

		$actions = Access::getActionsFromFile(JPATH_ADMINISTRATOR . '/components/com_meedya/access.xml');
//		$actions = Access::getActions($assetName);

		foreach ($actions as $action) {
			$result->{$action->name} = $user->authorise($action->name, $assetName);
		}

		return $result;
	}

	public static function getInstanceID ()
	{
		if (is_null(self::$instanceType)) self::getTypeOwner();
		return base64_encode(self::$instanceType.':'.self::$ownerID);
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
		if (!$isq) return self::componentOption('storQuota', 268435456);
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
		$units = ['B','KB','MB','GB','TB'];
		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);
		$bytes /= 1024 ** $pow;
		return round($bytes, $precision) . ' ' . $units[$pow];
	}

	private static function getTypeOwner ()
	{
		if (is_null(self::$instanceType)) {
			$app = Factory::getApplication();
			$id = $app->getInput()->getBase64('mID', false);
			if ($id) {
				$ids = explode(':',base64_decode($id));
				self::$instanceType = $ids[0];
				self::$ownerID = $ids[1];
			} else {
				$params = $app->getParams();
				self::$instanceType = $params->get('instance_type');
				switch (self::$instanceType) {
					case 0:
						self::$ownerID = Factory::getApplication()->getIdentity()->get('id');
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

	private static function componentOption (string $key, int $dflt)
	{
		static $co;

		if (empty($co)) {
			$co = ComponentHelper::getParams('com_meedya');
		}

		return $co->get($key, $dflt);
	}

}
