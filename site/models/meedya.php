<?php
defined('_JEXEC') or die;

class MeedyaModelMeedya extends JModelList
{
	protected $curAlbID = 0;
	protected $_album = null;

	public function __construct ($config = array())
	{
		$dbFile = '/meedya.db3';
		$udbPath = MeedyaHelper::userDataPath().$dbFile;
		$doInit = !file_exists($udbPath);
		
		$db = JDatabaseDriver::getInstance(array('driver'=>'sqlite','database'=>$udbPath));
		$db->connect();
		$db->getConnection()->sqliteCreateFunction('strtotime', 'strtotime', 1);

		if ($doInit) {
			require_once JPATH_COMPONENT.'/helpers/db.php';
			MeedyaHelperDb::buildDb($db);
		}

		$config['dbo'] = $db;
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
	//	$db = parent::getDBO();
		$db = $this->getDbo();
		$db->setQuery('SELECT `vals` FROM `config` WHERE `type`='.$db->quote($which));
		$r = $db->loadResult();
		return unserialize($r);
	}

	public function getItemThumbFile ($iid)
	{
	//	$db = parent::getDBO();
		$db = $this->getDbo();
		$db->setQuery('SELECT `file`,`thumb` FROM `meedyaitems` WHERE `id`='.$iid);
		$r = $db->loadAssoc();
		//var_dump($r);
		return $r['thumb'] ? $r['thumb'] : $r['file'];
	}

	public function getItemThumbFilePlus ($iid)
	{
	//	$db = parent::getDBO();
		$db = $this->getDbo();
		$db->setQuery('SELECT `file`,`thumb`,`title`,`desc` FROM `meedyaitems` WHERE `id`='.$iid);
		$r = $db->loadAssoc();
		$thm = $r['thumb'] ? $r['thumb'] : $r['file'];
		return array($thm, $r['title'], $r['desc']);
	}

	public function getAlbumsList ()
	{
	//	$db = parent::getDBO();
		$db = $this->getDbo();
		$db->setQuery('SELECT `aid`,`title`,`hord` FROM `albums`');
		$r = $db->loadAssocList();
		//var_dump($r);
		return $r;
	}

	protected function getListQuery ()
	{
	//	$db = parent::getDBO();
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('albums');
		$query->where('paid='.$this->curAlbID);
		return $query;
	}

	private function getAlbum ()
	{
		if (!$this->_album) {
			$items = parent::getItems();
			$this->_album = $items[0];
		}
	}

}