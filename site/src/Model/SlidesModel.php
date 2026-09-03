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
use Joomla\CMS\Component\ComponentHelper;

class SlidesModel extends MeedyaModel
{
	public $state;
	protected $_album;
	protected $_itms;
	protected $_total;

	public function getTitle ()
	{
		$aid = $this->getState('album.id') ? : 0;
		$db = $this->getDatabase();
		$db->setQuery('SELECT `title` FROM `albums` WHERE `aid`='.$aid);
		return $db->loadResult();
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
		$db = $this->getDatabase();
		$db->setQuery('SELECT * FROM `albums` WHERE `aid`='.$aid);
		$this->_album = $db->loadObject();
		return $this->_album;
	}

	public function getItemFile ($iid)
	{
		if (!$iid) return false;
		$db = $this->getDatabase();
		$db->setQuery('SELECT * FROM `meedyaitems` WHERE `id`='.$iid);
		//var_dump($r);
		return $db->loadAssoc();
	}

	public function getTotal ()
	{
		return $this->_total;
	}

	protected function getListQuery ()
	{	//echo'<xmp>';var_dump($this);echo'</xmp>';
		$aid = $this->getState('album.id') ? : 0;
		$db = $this->getDatabase();
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
		$params = ComponentHelper::getParams('com_meedya');
		$input = $app->getInput();

		// album ID
		$aid = $input->get('aid', 0, 'INT');	//echo'<xmp>';var_dump($this->state);echo'</xmp>';
		$this->state->set('album.id', $aid);

		// Load the parameters.
		$this->setState('params', $params);
	}

}
