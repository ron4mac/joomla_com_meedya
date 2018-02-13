<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2017 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

class MeedyaModelMeedya extends JModelList
{
	protected $curAlbID = 0;
	protected $_album = null;

	public function __construct ($config = array())
	{
		$dbFile = '/meedya.db3';
		$udbDir = MeedyaHelper::userDataPath();
		if (!$udbDir) {
			throw new Exception('ACCESS NOT ALLOWED', 403);
			//parent::__construct($config);
			//$this->setError('ACCESS NOT ALLOWED');
			//return;
		}
		$udbPath = $udbDir.$dbFile;
		$doInit = !file_exists($udbPath);

		try {
			$db = JDatabaseDriver::getInstance(array('driver'=>'sqlite','database'=>$udbPath));
			$db->connect();
			$dbc = $db->getConnection();
			$dbc->sqliteCreateFunction('strtotime', 'strtotime', 1);
			$dbc->sqliteCreateFunction('albhier', array($this,'albhier'), 2);
			$dbc->sqliteCreateFunction('inpsv', array($this,'inpsv'), 2);

			if ($doInit) {
				require_once JPATH_COMPONENT.'/helpers/db.php';
				MeedyaHelperDb::buildDb($db);
			}

			$config['dbo'] = $db;
		}
		catch (JDatabaseExceptionConnecting $e) {
			echo'<xmp>';var_dump($e);echo'</xmp>';
			jexit();
		}
		parent::__construct($config);
	}

/* * * * sqlite extension functions * * * */
	// a kluge to order albums so that sub-albums are listed just below their parents
	public function albhier ($aid, $paid)
	{
		if ($paid == 0) {
			return $aid * 1000;
		} else {
			return $paid * 1000 + $aid;
		}
	}
	// an album's items and an item's albums are in PSV (pipe separated variable) fields
	// this will determine if a speciific value is in the field
	public function inpsv ($n, $fld)
	{
		return in_array($n, explode('|',$fld));
	}
/* * * * * * * * * * * * * * * * * * * * * */


// override some parent functions

	// add form access to our database
//	public function getFilterForm($data = array(), $loadData = true)
//	{
//		$form = parent::getFilterForm($data, $loadData);
//		$form['_DB'] = 'XXYYZZ';
//		return $form;
//	}

	public function getAlbumItems ()
	{
		$this->getAlbum();
		$this->_itms = explode('|', $this->_album->items);
		$this->_total = count($this->_itms);
		$aid = $this->getState('album.id') ? : 0;
		$limit = $this->state->get('list.limit'.$aid);
		if ($limit) {
			return array_slice($this->_itms, $this->state->get('list.start'.$aid), $limit);
		} else {
			return array_slice($this->_itms, $this->state->get('list.start'.$aid));
		}
	}

	public function getCfg ($which)
	{
		$db = $this->getDbo();
		$db->setQuery('SELECT `vals` FROM `config` WHERE `type`='.$db->quote($which));
		$r = $db->loadResult();
		return json_decode($r, true);
	}

	public function getItemThumbFile ($iid)
	{
		$db = $this->getDbo();
		$db->setQuery('SELECT `file`,`thumb` FROM `meedyaitems` WHERE `id`='.$iid);
		$r = $db->loadAssoc();
		//var_dump($r);
		return $r['thumb'] ? $r['thumb'] : $r['file'];
	}

	public function getItemThumbFilePlus ($iid)
	{
		$db = $this->getDbo();
		$db->setQuery('SELECT `file`,`thumb`,`title`,`desc` FROM `meedyaitems` WHERE `id`='.$iid);
		$r = $db->loadAssoc();
		$thm = $r['thumb'] ? $r['thumb'] : $r['file'];
		return array($thm, $r['title'], $r['desc']);
	}

	public function getAlbumsList ()
	{
		$db = $this->getDbo();
		$db->setQuery('SELECT * FROM `albums`');
		$r = $db->loadObjectList();
		//var_dump($r);
		return $r;
	}

	protected function getListQuery ()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('albums');
		$query->where('paid='.$this->curAlbID);
		return $query;
	}

/*	public function getAlbum ($aid=0)
	{
		if ($this->_album) return $this->_album;
		$aid = $aid ?: ($this->state->get('album.id') ?: 0);
		$db = $this->getDbo();
		$db->setQuery('SELECT * FROM `albums` WHERE `aid`='.$aid);
		$this->_album = $db->loadObject();
		return $this->_album;
	}*/

	public function getItemFile ($iid)
	{
		if (!$iid) return false;
		$db = $this->getDbo();
		$db->setQuery('SELECT * FROM `meedyaitems` WHERE `id`='.$iid);
		$r = $db->loadAssoc();
		//var_dump($r);
		return $r;
	}

	private function getAlbum ()
	{
		if (!$this->_album) {
			$items = parent::getItems();
			$this->_album = $items[0];
		}
	}

}