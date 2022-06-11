<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

require_once __DIR__ . '/meedya.php';

class MeedyaModelAlbum extends MeedyaModelMeedya
{
	protected $_album = null;
	protected $_itms = null;
	protected $_total = null;
	protected $_pagination = null;

	public function __construct ($config = [])
	{
		if (RJC_DBUG) { MeedyaHelper::log('MeedyaModelAlbum'); }
		parent::__construct($config);
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
		$this->_itms = explode('|', $this->_album->items);
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
		foreach ($albs as $k => $alb) {
			$albs[$k]->link = '&view=album&aid='.$alb->aid;
		}
		return $albs;
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

			$this->_pagination = new JPagination($total, $limitstart, $limit);
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
		$params = JComponentHelper::getParams('com_meedya');
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
