<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2023-2026 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.6.0
*/
namespace RJCreations\Component\Meedya\Site\Model;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\Database\DatabaseDriver;
use RJCreations\Library\RJUserCom;

class PicframeModel extends BaseDatabaseModel
{
	protected $db;
	protected $imgp = '';
	protected $curAlbID = 0;
	protected $_album;
	protected $galinst;
	protected $udp;

	public function __construct ($config=[])
	{
		if (empty($config['dbo']) && $config['inst']) {
			$dbFile = '/meedya.db3';
			$this->udp = RJUserCom::getStoragePath($config['inst']);
			$udbPath = $this->udp.$dbFile;
			try {
				$db = DatabaseDriver::getInstance(['driver'=>'sqlite','database'=>$udbPath]);
				$db->connect();
				$this->db = $db;
				$config['dbo'] = $db;
			}
			catch (\Exception $e) {
				echo'<xmp>';var_dump($e);echo'</xmp>';
				jexit();
			}
		}
		parent::__construct($config);
	}

	public function getPlayList ($aid, $recur, $inst)
	{
		$this->galinst = base64_encode(json_encode($inst));
		$this->imgp = Uri::root() . $this->udp . '/med/';
		return $this->getAlbImgs($aid);
	}

	public function getPlayListCount ($aid, $recur, $inst)
	{
		$this->galinst = base64_encode(json_encode($inst));
		$this->imgp = Uri::root() . $this->udp . '/med/';
		return $this->getAlbImgCount($aid);
	}

	public function getThumbnails ($aid, $recur, $inst)
	{
		$this->galinst = base64_encode(json_encode($inst));
		return $this->getAlbImgThms($aid);
	}

	public function getFramePic ($iid)
	{
		$pic = $this->getItemFile($iid);
		return $this->udp . '/med/' . $pic['file'];
	}

	private function getAlbImgs ($aid): array
	{
		$url = Route::_('index.php?option=com_meedya&format=raw&task=DispRaw.p4f&p=', false, 0, true);
	//	$url = JUri::root() . 'picframe.php/?gi=';
		$this->db->setQuery('SELECT items FROM albums WHERE aid='.$aid);
		if (!$ilst = trim($this->db->loadResult()?:'')) return [];
		$itms = explode('|', $ilst);
		$items = [];
		foreach ($itms as $iid) {
			$itm = $this->getItemFile($iid);
			if (str_starts_with($itm['mtype'], 'image/')) {
				$items[] = $url . $this->galinst . '.' . $iid;
			}
		}
		return $items;
	}

	private function getAlbImgCount ($aid): array|int
	{
		Route::_('index.php?option=com_meedya&format=raw&task=DispRaw.p4f&p=', false, 0, true);
	//	$url = JUri::root() . 'picframe.php/?gi=';
		$this->db->setQuery('SELECT items FROM albums WHERE aid='.$aid);
		if (!$ilst = trim($this->db->loadResult()?:'')) return [];
		$itms = explode('|', $ilst);
		$items = 0;
		foreach ($itms as $iid) {
			$itm = $this->getItemFile($iid);
			if (str_starts_with($itm['mtype'], 'image/')) {
				$items++;
			}
		}
		return $items;
	}

	private function getAlbImgThms ($aid): array
	{
		$url = Uri::root() . $this->udp . '/thm/';
		$this->db->setQuery('SELECT items FROM albums WHERE aid='.$aid);
		if (!$ilst = trim($this->db->loadResult()?:'')) return [];
		$itms = explode('|', $ilst);
		$items = [];
		foreach ($itms as $iid) {
			$itm = $this->getItemFile($iid);
			if (str_starts_with($itm['mtype'], 'image/')) {
				$items[] = ['iid'=>$iid, 'src'=>$url.$itm['file']];
			}
		}
		return $items;
	}

	private function getItemFile ($iid)
	{
		if (!$iid) return false;
		$this->db->setQuery('SELECT * FROM `meedyaitems` WHERE `id`='.$iid);
		//var_dump($r);
		return $this->db->loadAssoc();
	}


}