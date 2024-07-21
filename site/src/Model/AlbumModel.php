<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2024 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.4.0
*/
namespace RJCreations\Component\Meedya\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Component\ComponentHelper;

class AlbumModel extends MeedyaModel
{
	protected $_album = null;
	protected $_itms = null;
	protected $_total = null;
	protected $_pagination = null;

	public function __construct ($config = [], $factory = null)
	{
		if (RJC_DBUG) { \MeedyaHelper::log('MeedyaModelAlbum'); }
		parent::__construct($config, $factory);
	}

	public function getTitle ()
	{
		$this->getAlbum();
		return $this->_album->title;
	}

	public function getDesc ()
	{
		$this->getAlbum();
		return $this->_album->desc;
	}

	public function getItems ()
	{
		$this->getAlbum();
		if (!trim($this->_album->items ?: '')) return [];
		$this->_itms = explode('|', $this->_album->items?:'');
		$this->_total = count($this->_itms);
		$aid = $this->getState('album.id') ? : 0;
		$limit = $this->state->get('list.limit'.$aid);
		if ($limit) {
		//	return array_slice($this->_itms, $this->state->get('list.start'.$aid), $limit);
			$iids = array_slice($this->_itms, $this->state->get('list.start'.$aid), $limit);
		} else {
		//	return array_slice($this->_itms, $this->state->get('list.start'.$aid));
			$iids = array_slice($this->_itms, $this->state->get('list.start'.$aid));
		}
		$items = [];
		foreach ($iids as $iid) {
			$items[] = $this->getItemFile($iid);
		}
		return $items;
	}

	public function getAlbums ()
	{
		$aid = $this->getState('album.id') ? : 0;
		$db = $this->getDbo();
		$db->setQuery('SELECT * FROM `albums` WHERE `paid`='.$aid);
		$albs = $db->loadObjectList();
		foreach ($albs as &$alb) {
			$alb->link = '&view=album&aid='.$alb->aid;
			$alb->isClone = false;
			if ($alb->items && substr($alb->items,0,1)=='*') {
				$alb->isClone = true;
				$alb->oaid = (int) substr($alb->items,1);
			} else {
			//	$alb->items = $alb->items ? count(explode('|',$alb->items)) : 'no';
			}
		}
		return $albs;
	}

	private function getAlbImgs ($db, $aid)
	{
		$db->setQuery('SELECT items FROM albums WHERE aid='.$aid);
		if (!$ilst = trim($db->loadResult()?:'')) return [];
		$itms = explode('|', $ilst);
		$items = [];
		foreach ($this->_itms as $iid) {
			$itm = $this->getItemFile($iid);
			if (substr($itm['mtype'],0,6) == 'image/') {
				$items[] = $urlp . $itm['file'];
			}
		}
		return $items;
	}

	public function getPlaylist ($aid, $full, $recur, $inst)
	{
		$db = null;
		$dbFile = '/meedya.db3';
		$udp = RJUserCom::getStoragePath($inst);	echo $udp; jexit();
		$udbPath = $udp.$dbFile;
		try {
			$db = JDatabaseDriver::getInstance(['driver'=>'sqlite','database'=>$udbPath])->connect();
		}
		catch (JDatabaseExceptionConnecting $e) {
			echo'<xmp>';var_dump($e);echo'</xmp>';
			jexit();
		}
		$urlp = JUri::root() . \MeedyaHelper::userDataPath() . '/med/';
		$items = [];
		foreach ($this->_itms as $iid) {
			$itm = $this->getItemFile($iid);
			if (substr($itm['mtype'],0,6) == 'image/') {
				$items[] = $urlp . $itm['file'];
			}
		}
		return $items;
	}

	public function getImageUrl ()
	{

	}

	public function getTotal ()
	{
		return $this->_total;
	}

	public function getPagination ()
	{
		if (empty($this->_pagination)) {
			$aid = $this->getState('album.id') ? : 0;
			$limitstart = $this->state->get('list.start'.$aid);
			$limit = $this->state->get('list.limit'.$aid);
			$total = $this->getTotal();

			$this->_pagination = new Pagination($total, $limitstart, $limit);
		}

		return $this->_pagination;
	}

	protected function getListQuery ()
	{	//echo'<xmp>';var_dump($this);echo'</xmp>';
		$aid = $this->getState('album.id') ? : 0;
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('albums');
		$query->where('aid='.$aid);
		return $query;
	}

	protected function populateState ($ordering = null, $direction = 'ASC')
	{	//echo'####POPSTATE####';
		// Initialise variables.
		$app = Factory::getApplication();
		$params = ComponentHelper::getParams('com_meedya');
		$input = $app->input;

		// album ID
		$aid = $input->get('aid', 0, 'INT');
		$this->state->set('album.id', $aid);	//echo'<xmp>';var_dump($this->state);echo'</xmp>';

		// List state information
		$limit = 0;	//$app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		$this->setState('list.limit'.$aid, $limit);

		$limitstart = $input->getInt('limitstart', 0);
		$this->setState('list.start'.$aid, $limitstart);

		// Load the parameters.
		$this->setState('params', $params);

		parent::populateState($ordering, $direction);
	}

	private function getAlbum ()
	{
		if (!$this->_album) {
			$items = parent::getItems();
			$this->_album = $items[0];
		}
	}

}
