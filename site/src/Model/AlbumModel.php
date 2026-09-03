<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2026 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.6.0
*/
namespace RJCreations\Component\Meedya\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Component\ComponentHelper;
use RJCreations\Component\Meedya\Site\Helper\MeedyaHelper;

class AlbumModel extends MeedyaModel
{
	public $state;
	protected $_album;
	protected $_itms;
	protected $_total;
	protected $_pagination;
	protected $shraid;

	public function __construct ($config = [], $factory = null)
	{
		if (RJC_DBUG) {
			MeedyaHelper::log('MeedyaModelAlbum');
			MeedyaHelper::log(print_r($config,true));
		}
		if (!empty($config['inst'])) {
			$this->shraid = $config['inst']->aid;
		}
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
		$db = $this->getDatabase();
		$db->setQuery('SELECT * FROM `albums` WHERE `paid`='.$aid);
		$albs = $db->loadObjectList();
		foreach ($albs as &$alb) {
			$alb->link = '&view=album&aid='.$alb->aid;
			$alb->isClone = false;
			if ($alb->items && str_starts_with($alb->items, '*')) {
				$alb->isClone = true;
				$alb->oaid = (int) substr($alb->items,1);
			} else {
			//	$alb->items = $alb->items ? count(explode('|',$alb->items)) : 'no';
			}
		}
		return $albs;
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
	{
		$aid = $this->getState('album.id') ? : 0;
		$db = $this->getDatabase();
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
		$input = $app->getInput();

		// album ID
		$aid = $this->shraid ?: $input->get('aid', 0, 'INT');
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

	private function getAlbum (): void
	{
		if (!$this->_album) {
			$items = parent::getItems();
			$this->_album = $items[0];
		}
	}

}
