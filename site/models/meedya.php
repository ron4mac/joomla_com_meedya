<?php
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
			$db->getConnection()->sqliteCreateFunction('strtotime', 'strtotime', 1);

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
		return unserialize($r);
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
		$db->setQuery('SELECT `aid`,`title`,`hord` FROM `albums`');
		$r = $db->loadAssocList();
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