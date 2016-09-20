<?php
/**
 * @package    com_meedya
 * @copyright  Copyright (C) 2016 RJCreations - All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.application.component.modellist');

class MeedyaModelMeedya extends JModelList
{
	protected $relm = 'u';
	protected $_total = -1;

	public function __construct($config = array())
	{   
		$config['filter_fields'] = array('fullname', 'username', 'userid');
		parent::__construct($config);
	}

	public function getItems ()			//	count(glob("/path/to/file/[!\.]*"));
	{	//return array();
		// Get a storage key.
		$store = $this->getStoreId('list');

		// Try to load the data from internal storage.
		if (isset($this->cache[$store])) {
			return $this->cache[$store];
		}

		$unotes = array();
		$folds = MeedyaHelper::getDbPaths($this->relm, 'meedya', true);
		foreach ($folds as $dir => $path) {
			$userid = (int)substr($dir,1);
			$files = count(glob(dirname($path).'/img/[!\.]*')) -1;
			if ($this->relm == 'u') {
				$user = JUser::getInstance($userid);
				$unotes[] = array('name'=>$user->name,'uname'=>$user->username,'uid'=>$userid, 'fcount'=>$files);
			} else {
				$unotes[] = array('uname'=>MeedyaHelper::getGroupTitle($userid),'name'=>'group','uid'=>$userid, 'fcount'=>$files);
			}
		}
		$this->_total = count($unotes);

		$start = $this->getState('list.start');
		$limit = $this->getState('list.limit');
		$listOrder = $this->getState('list.ordering');
		$listDirn = $this->getState('list.direction');
	//	echo $listOrder;echo $listDirn;

		foreach ($unotes as $key => $row) {
			$name[$key]  = $row['name'];
			$uname[$key] = $row['uname'];
			$uid[$key] = $row['uid'];
			$fcount[$key] = $row['fcount'];
		}
		
		if ($this->_total)
		// Sort the data with volume descending, edition ascending
		// Add $data as the last parameter, to sort by the common key
		switch ($listOrder) {
			case 'username':
				array_multisort($uname, SORT_ASC, $name, SORT_ASC, $uid, SORT_ASC, $unotes);
				break;
			case 'fullname':
				array_multisort($name, SORT_ASC, $uname, SORT_ASC, $uid, SORT_ASC, $unotes);
				break;
			case 'userid':
				array_multisort($uid, SORT_ASC, $uname, SORT_ASC, $name, SORT_ASC, $unotes);
				break;
			case 'fcount':
				array_multisort($fcount, SORT_ASC, $uname, SORT_ASC, $name, SORT_ASC, $unotes);
				break;
		}


		// Add the items to the internal cache.
		$this->cache[$store] = array_slice($unotes, $start, $limit ? $limit : null);

		return $this->cache[$store];
	}

	public function getTotal ()
	{
		// Get a storage key.
		$store = $this->getStoreId('getTotal');

		// Try to load the data from internal storage.
		if (isset($this->cache[$store])) {
			return $this->cache[$store];
		}

		// Load the total if none
		if ($this->_total < 0) $this->getItems();

		// Add the total to the internal cache.
		$this->cache[$store] = $this->_total;

		return $this->cache[$store];
	}

	protected function populateState ($ordering = null, $direction = null) {
		parent::populateState('username', 'ASC');
	}

}
