<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

use Joomla\CMS\User\User;

class MeedyaModelMeedya extends JModelList
{
	protected $relm = 'u';
	protected $_total = -1;

	public function __construct($config = [])
	{
		$config['filter_fields'] = ['fullname', 'username', 'userid'];
		parent::__construct($config);
	}

	public function getItems ()			//	count(glob("/path/to/file/[!\.]*"));
	{	//return [];
		// Get a storage key.
		$store = $this->getStoreId('list');

		// Try to load the data from internal storage.
		if (isset($this->cache[$store])) {
			return $this->cache[$store];
		}

		$unotes = [];
		$folds = MeedyaAdminHelper::getDbPaths($this->relm, 'meedya', true);
		foreach ($folds as $dir => $mgis) foreach ($mgis as $mgi) {
			$dbok = MeedyaHelperDb::checkDbVersion($mgi['path']);
			$dbwarn = $dbok ? '' : '<span style="color:red"> DB NEEDS UPDATE</span>';
			$userid = (int)substr($dir,1);
		$char1 = substr($dir,0,1);
			$files = count(glob(dirname($mgi['path']).'/img/[!\.]*')) -1;
		//	if ($this->relm == 'u') {
			if ($char1 == '@') {
				$user = User::getInstance($userid);
			//	$unotes[] = ['name'=>$user->name,'uname'=>$user->username,'uid'=>$userid,'mnu'=>$mgi['mnu'],'fcount'=>$files];
				$unotes[] = ['name'=>$user->name,'uname'=>$user->username,'uid'=>$dir,'mnun'=>$mgi['mnun'],'fcount'=>$files,'info'=>$dbwarn];
			} else {
			//	$unotes[] = ['uname'=>MeedyaAdminHelper::getGroupTitle($userid),'name'=>'group','uid'=>$userid,'mnu'=>$mgi['mnu'],'fcount'=>$files];
				$unotes[] = ['uname'=>MeedyaAdminHelper::getGroupTitle($userid),'name'=>'group','uid'=>$dir,'mnun'=>$mgi['mnun'],'fcount'=>$files,'info'=>$dbwarn];
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
