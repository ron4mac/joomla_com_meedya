<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2023 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.3.2
*/
defined('_JEXEC') or die;

class MeedyaModelPicframe extends Joomla\CMS\MVC\Model\BaseDatabaseModel
{
	protected $db = null;
	protected $imgp = '';
	protected $curAlbID = 0;
	protected $_album = null;
	protected $galinst;
	protected $udp;

	public function __construct ($config=[])
	{
		if (empty($config['dbo']) && $config['inst']) {
			$dbFile = '/meedya.db3';
			$this->udp = RJUserCom::getStoragePath($config['inst']);
			$udbPath = $this->udp.$dbFile;
			try {
				$db = JDatabaseDriver::getInstance(['driver'=>'sqlite','database'=>$udbPath]);
				$db->connect();
				$this->db = $db;
				$config['dbo'] = $db;
			}
			catch (JDatabaseExceptionConnecting $e) {
				echo'<xmp>';var_dump($e);echo'</xmp>';
				jexit();
			}
		}
		parent::__construct($config);
	}

	public function getPlayList ($aid, $recur, $inst)
	{
		$this->galinst = base64_encode(json_encode($inst));
//		$dbFile = '/meedya.db3';
//		$udp = RJUserCom::getStoragePath($inst);	//echo $udp; jexit();
		$this->imgp = JUri::root() . $this->udp . '/med/';
//		$udbPath = $udp.$dbFile;
//		try {
//			$db = JDatabaseDriver::getInstance(['driver'=>'sqlite','database'=>$udbPath]);
//			$db->connect();
//			$this->db = $db;
			return $this->getAlbImgs($aid);
//		}
//		catch (JDatabaseExceptionConnecting $e) {
//			echo'<xmp>';var_dump($e);echo'</xmp>';
//			jexit();
//		}
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

	private function getAlbImgs ($aid)
	{
		$url = Joomla\CMS\Router\Route::_('index.php?option=com_meedya&format=raw&task=p4f&p=', false, 0, true);
	//	$url = JUri::root() . 'picframe.php/?gi=';
		$this->db->setQuery('SELECT items FROM albums WHERE aid='.$aid);
		if (!$ilst = trim($this->db->loadResult()?:'')) return [];
		$itms = explode('|', $ilst);
		$items = [];
		foreach ($itms as $iid) {
			$itm = $this->getItemFile($iid);
			if (substr($itm['mtype'],0,6) == 'image/') {
				$items[] = $url . $this->galinst . '.' . $iid;
			}
		}
		return $items;
		foreach ($itms as $iid) {
			$itm = $this->getItemFile($iid);
			if (substr($itm['mtype'],0,6) == 'image/') {
				$items[] = $this->imgp . $itm['file'];
			}
		}
		return $items;
	}

	private function getAlbImgThms ($aid)
	{
		$url = JUri::root() . $this->udp . '/thm/';
		$this->db->setQuery('SELECT items FROM albums WHERE aid='.$aid);
		if (!$ilst = trim($this->db->loadResult()?:'')) return [];
		$itms = explode('|', $ilst);
		$items = [];
		foreach ($itms as $iid) {
			$itm = $this->getItemFile($iid);
			if (substr($itm['mtype'],0,6) == 'image/') {
				$items[] = $url . $itm['file'];
			}
		}
		return $items;
	}

	private function getItemFile ($iid)
	{
		if (!$iid) return false;
		$this->db->setQuery('SELECT * FROM `meedyaitems` WHERE `id`='.$iid);
		$r = $this->db->loadAssoc();
		//var_dump($r);
		return $r;
	}


}