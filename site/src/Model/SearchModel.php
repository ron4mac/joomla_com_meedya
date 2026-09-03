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
use RJCreations\Component\Meedya\Site\Helper\MeedyaHelper;

class SearchModel extends MeedyaModel
{
	public $state;
	public $filterFormName;
	protected $_album;
	protected $_itms;
	protected $_total;

	public function search ($sterm, $aid)
	{
		$db = $this->getDatabase();
		$sterm = $db->escape($sterm);

		$terms = ['`kywrd` MATCH \''.$sterm.'\'','`title` MATCH \''.$sterm.'\'','`desc` MATCH \''.$sterm.'\''];

		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('meedyaitems');
		if ($aid) {
			$query->where('inpsv('.$aid.',`album`)')->andWhere($terms,'OR');
		} else {
			$query->where($terms,'OR');
		}
		if (RJC_DBUG) MeedyaHelper::log('ModelSearch search', (string)$query);

		$db->setQuery($query);
		//var_dump($r);
		return $db->loadAssocList();
	}

	public function getTitle ()
	{
		$aid = $this->getState('album.id') ? : 0;
		$db = $this->getDatabase();
		$db->setQuery('SELECT `title` FROM `albums` WHERE `aid`='.$aid);
		return $db->loadResult();
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

	protected function _getListQuery ()
	{	//echo'<xmp>';var_dump($this);echo'</xmp>';
		$aid = $this->getState('album.id') ? : 0;
		$db = $this->getDatabase();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('albums');
		$query->where('aid='.$aid);
		return $query;
	}

	protected function getListQuery ()
	{
		//echo '<xmp>';var_dump($this->state);echo'</xmp>';
		if ($this->filterFormName !== 'filter_images') {
			$db = $this->getDatabase();
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('albums');
			$aid = $this->state->get('album.id', 0);
			if ($aid) {
//				$query->where('aid='.$aid);
			if ($aid < 0) {
				$query->where('album IS NULL OR album=\'\'');
			} else {
				$query->where('album='.$aid);
			}
			}
			if (RJC_DBUG) MeedyaHelper::log('ModelManage getListQuery(items)', $query);
			return $query;
		}

		$db = $this->getDatabase();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('meedyaitems');
		$aid = $this->state->get('filter.album', 0);
		if ($aid) {
			if ($aid < 0) {
				$query->where('album IS NULL OR album=\'\'');
			} else {
				$query->where('inpsv('.$aid.',`album`)');
			}
		}
		$tag = $this->state->get('filter.tag', '');
		if ($tag) {
			$tag = $db->escape($tag);
			$query->where('`kywrd` LIKE \'%'.$tag.'%\'');
		}
		$search = $this->state->get('filter.search', '');
		if ($search) {
			$search = $db->escape($search);
			$query->where('`title`||\' \'||`desc` LIKE \'%'.$search.'%\'');
		}
		$query->order('expodt');
		//echo '<xmp>';var_dump($query);echo'</xmp>';

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
