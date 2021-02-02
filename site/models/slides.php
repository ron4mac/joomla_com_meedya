<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2020 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

require_once __DIR__ . '/meedya.php';

class MeedyaModelSlides extends MeedyaModelMeedya
{
	protected $_album = null;
	protected $_itms = null;
	protected $_total = null;

	public function getTitle ()
	{
		$aid = $this->getState('album.id') ? : 0;
		$db = $this->getDbo();
		$db->setQuery('SELECT `title` FROM `albums` WHERE `aid`='.$aid);
		$r = $db->loadResult();
		return $r;
	}

	public function getItems ()
	{
		// Invoke the parent getItems method to get the main list
		$items = parent::getItems();
		$this->_album = $items[0];
		//echo'<xmp>';var_dump($items);echo'</xmp>';jexit();
		if (!$this->_album->items) return false;
		$this->_itms = explode('|', $this->_album->items);
		$this->_total = count($this->_itms);
		return $this->_itms;
	}

	public function getAlbum ($aid=0)
	{
		if ($this->_album) return $this->_album;
		$aid = $aid ?: ($this->state->get('album.id') ?: 0);
		$db = $this->getDbo();
		$db->setQuery('SELECT * FROM `albums` WHERE `aid`='.$aid);
		$this->_album = $db->loadObject();
		return $this->_album;
	}

	public function getItemFile ($iid)
	{
		if (!$iid) return false;
		$db = $this->getDbo();
		$db->setQuery('SELECT * FROM `meedyaitems` WHERE `id`='.$iid);
		$r = $db->loadAssoc();
		//var_dump($r);
		return $r;
	}

	public function getTotal ()
	{
		return $this->_total;
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

	protected function populateState ($ordering = null, $direction = null)
	{	//echo'####POPSTATE####';
		// Initialise variables.
		$app = Factory::getApplication();
		$params = JComponentHelper::getParams('com_meedya');
		$input = $app->input;

		// album ID
		$aid = $input->get('aid', 0, 'INT');	//echo'<xmp>';var_dump($this->state);echo'</xmp>';
		$this->state->set('album.id', $aid);

		// Load the parameters.
		$this->setState('params', $params);
	}

}
