<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2023-2024 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.4.0
*/
namespace RJCreations\Component\Meedya\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Component\ComponentHelper;
use RJCreations\Component\Meedya\Site\Helper\MeedyaHelper;

class PublicModel extends ListModel
{
	protected $pubalbs = [];
	protected $pubdbs = [];
	protected $curAlbID = 0;
	protected $_album = null;
	protected $sdp = null;		// storage directory path
	protected $full_gallery;

	public function __construct ($config = [], $factory = null)
	{
	//	$dbFile = '/meedya.db3';
		$app = Factory::getApplication();
		$result = $app->triggerEvent('onRjuserDatapath');
		$this->sdp = trim($result[0] ?? 'userstor');
		$this->full_gallery = $app->getParams()->get('full_gallery', 0);
		$this->scanForDbs($this->sdp);
	//	var_dump($this->pubalbs);
		parent::__construct($config, $factory);
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
	//
	//
	public function match ($pat, $fld)
	{
		$vals = explode(' ', trim($pat));
		return preg_match('#'.implode('|', $vals).'#i', $fld);
	}
/* * * * * * * * * * * * * * * * * * * * * */


// override some parent functions

	// add form access to our database
//	public function getFilterForm($data = [], $loadData = true)
//	{
//		$form = parent::getFilterForm($data, $loadData);
//		$form['_DB'] = 'XXYYZZ';
//		return $form;
//	}

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

	public function getAlbums ()
	{
		list($gdir, $gsfx, $gaid) = explode('|', base64_decode($this->getState('pgid')));
		$aid = $this->getState('album.id') ? : 0;
		$db = $this->getDb();
		$db->setQuery('SELECT * FROM `albums` WHERE `paid`='.$gaid);
		$albs = $db->loadObjectList();
		$db->disconnect();
		foreach ($albs as $k => $alb) {
			$albs[$k]->link = '&view=public&layout=album&pgid='.base64_encode(implode('|',[$gdir,$gsfx,$alb->aid]));
		}
		return $albs;
	}



	public function getItems ()
	{
		if ($this->full_gallery) return $this->pubdbs;
 		$items = [];
		foreach ($this->pubalbs as $k => $padb) {
			$db = DatabaseDriver::getInstance(['driver'=>'sqlite','database'=>$padb['path'].'/meedya.db3']);
			$db->connect();
			if ($this->full_gallery) {
				$db->setQuery('SELECT * FROM `albums` WHERE `aid`='.$padb['aid'].' AND `hord` IS "1"');
			} else {
				$db->setQuery('SELECT * FROM `albums` WHERE `aid`='.$padb['aid']);
			}
			$alb = $db->loadObject();
			$db->disconnect();
			if (!$alb) continue;
			$alb->paix = $k;
			$alb->path = $padb['path'];
			$alb->owner = $padb['owner'];
			$items[] = $alb;
		}
		return $items;
	}


	public function getAlbumItems ()
	{	//echo $this->getState('pgid'); return [];
		//$this->curAlbID = $this->getState('album.id') ? : 0;
		$this->getAlbum();
	//	if (RJC_DBUG) MeedyaHelper::log('ModelMeedya getAlbumItems', debug_backtrace(2));
		$this->_itms = explode('|', $this->_album->items?:'');
		$this->_total = count($this->_itms);
		$aid = $this->getState('album.id') ? : 0;
		$limit = $this->state->get('list.limit'.$aid, null);
		$iids = array_slice($this->_itms, $this->state->get('list.start'.$aid, 0), $limit);
		$items = [];
		foreach ($iids as $iid) {
			$items[] = $this->getItemFile($iid);
		}
		return $items;
	}


	public function _getCfg ($which)
	{
		$db = $this->getDbo();
		$db->setQuery('SELECT `vals` FROM `config` WHERE `type`='.$db->quote($which));
		$r = $db->loadResult();
		return json_decode($r, true);
	}

	public function getItemThumbFile ($iid, $paix)
	{
	//	$db = $this->getDb(empty($paix)?false:$paix);
		$db = $this->getDb($paix);
		$db->setQuery('SELECT `file`,`thumb` FROM `meedyaitems` WHERE `id`='.$iid);
		$r = $db->loadAssoc();
		$db->disconnect();
		//var_dump($r);
		return $r['thumb'] ? $r['thumb'] : $r['file'];
	}

	public function getItemThumbFilePlus ($iid)
	{
		$db = $this->getDb();
		$db->setQuery('SELECT `file`,`mtype`,`thumb`,`title`,`desc` FROM `meedyaitems` WHERE `id`='.$iid);
		$r = $db->loadAssoc();
		$db->disconnect();
		$thm = $r['thumb'] ? $r['thumb'] : $r['file'];
		return [$thm, $r['title'], $r['desc'], $r['mtype']];
	}

	public function _getAlbumsList ()
	{
		$db = $this->getDbo();
		$db->setQuery('SELECT * FROM `albums`');
		$r = $db->loadObjectList();
		//var_dump($r);
		return $r;
	}

	// returns an array of aid=>title to the specified album
	public function getAlbumPath ()
	{
		$pgid = $this->getState('pgid');
		list($gdir, $gsfx, $to) = explode('|', base64_decode($pgid));
		$db = $this->getDb();
		$albs = [];
		$inpub = false;
		while ($to) {
			$db->setQuery('SELECT paid,visib,title FROM albums WHERE aid='.$to);
			$r = $db->loadAssoc();	//var_dump($r);echo'<br>';
			if (!$inpub) array_unshift($albs, [$to =>[$r['title'],$pgid]]);
			if (!$inpub && $r['visib']) $inpub = true;
			$to = $r['paid'];
			$pgid = base64_encode(implode('|',[$gdir,$gsfx,$to]));
		}
		$db->disconnect();
		return $albs;
	}

	public function getGallpath ($paix=false)
	{
		if ($paix===false) {
			list($gdir, $gsfx, $gaid) = explode('|', base64_decode($this->getState('pgid')));
			$path = $this->sdp.'/'.$gdir.'/com_meedya'.$gsfx;
			return $path;
		} else {
			return $this->pubalbs[$paix]['path'];
		}
	}

	protected function _getListQuery ()
	{
		$albord = ['`tstamp` DESC','`tstamp` ASC','`title` DESC','`title` ASC'];
		$params = Factory::getApplication()->getParams();
		$ordopt = (int)$params->get('album_order', 0);
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('albums');
		$query->where('paid='.$this->curAlbID);
		$query->order($albord[$ordopt]);
		if (RJC_DBUG) MeedyaHelper::log('ModelMeedya getListQuery', (string)$query);
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
		$db = $this->getDb();
		$db->setQuery('SELECT * FROM `meedyaitems` WHERE `id`='.$iid);
		$r = $db->loadAssoc();
		//var_dump($r);
		return $r;
	}

	public function getOwnerName ($own)
	{
		$owner = '';
		switch ($own[0]) {
			case '@':
				$user = Factory::getUser(substr($own,1));
				$owner = $user->name;
				break;
			case '_':
				$gid = substr($own,1);
				if ($gid=='') $gid = 0;
				if ($gid==0) {
					$owner = 'Site';
					break;
				}
				$db = Factory::getDbo();
				$db->setQuery('SELECT title FROM #__usergroups WHERE id='.$gid);
				$owner = $db->loadResult() ?: 'Group';
			//$owner='?WTF?'.$own.'|'.$gid.'.';
				break;
		}
		return $owner;
	}


//	protected function populateState ($ordering = null, $direction = null)
//	{
//		parent::populateState($ordering, $direction);
//		$this->setState('list.limit', 0);
//	}


	protected function populateState ($ordering = null, $direction = null)
	{
		// Initialize variables
		$app = Factory::getApplication();
		$params = ComponentHelper::getParams('com_meedya');
		$input = $app->input;

		// menu params
		$mparams = $app->getParams();
		$this->setState('maxUpload', (int)$mparams->get('maxUpload', 0));

		// album ID
		$pid = $input->get('pid', 0, 'INT');
		$this->state->set('parent.id', $pid);

		// public gallery id
		$pgid = $input->get('pgid','','BASE64');
		$this->state->set('pgid', $pgid);

		// List state information
//		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
//		$this->setState('list.limit'.$pid, $limit);

//		$limitstart = $input->getInt('limitstart', 0);
//		$this->setState('list.start'.$pid, $limitstart);

		// Load the parameters.
		$this->setState('cparams', $params);

		parent::populateState($ordering, $direction);
	}


	private function getDb ($paix=false)
	{
		if ($paix===false) {
			list($gdir, $gsfx, $gaid) = explode('|', base64_decode($this->getState('pgid')));
			$path = $this->sdp.'/'.$gdir.'/com_meedya'.$gsfx;
		} else {
			$path = $this->pubalbs[$paix]['path'];
		}
		if (!file_exists($path.'/meedya.db3')) return null;
		$db = DatabaseDriver::getInstance(['driver'=>'sqlite','database'=>$path.'/meedya.db3']);
		$db->connect();
		return $db;
	}


	private function scanForDbs ($path)
	{
		// Check directory exists or not
		if (file_exists($path) && is_dir($path)) {
			// Scan the files in this directory
			$result = scandir($path);

			// Filter out the current (.) and parent (..) directories
			$files = array_diff($result, ['.', '..']);

			if (count($files) > 0) {
				// Loop through retuned array
				foreach ($files as $file) {
					if (is_file("$path/$file")) {
						// do nothing
					} else if (is_dir("$path/$file")) {
						if (substr($file,0,11)=='com_meedya_') $this->checkPublic("$path/$file");
						else $this->scanForDbs("$path/$file");
					}
				}
			}
		}
	}


	private function checkPublic ($path)
	{
		if (!file_exists($path.'/meedya.db3')) return;
		$own = basename(dirname($path));
		$owner = $this->getOwnerName($own);
		$db = DatabaseDriver::getInstance(['driver'=>'sqlite','database'=>$path.'/meedya.db3']);
		$db->connect();
		if ($this->full_gallery) {	echo'FULL FULL ';
			$db->setQuery('SELECT aid,ownid FROM `albums` ORDER BY `hord`');
		} else {	//echo'NOT FULL ';
			$db->setQuery('SELECT aid,ownid FROM `albums` WHERE `visib`>0 ORDER BY `hord`');
		}
		$albs = $db->loadAssocList();
		if ($albs) $this->pubdbs[] = (object)['path'=>$path, 'owner'=>$owner];
		foreach ($albs as $alb) {
			$alb['path'] = $path;
			$alb['owner'] = $owner;
			$this->pubalbs[] = $alb;
		}
		$db->disconnect();
	}


	private function getAlbum ()
	{
		if (!$this->_album) {
			$gaid = explode('|', base64_decode($this->getState('pgid')))[2];
			$db = $this->getDb();
			$db->setQuery('SELECT * FROM `albums` WHERE `aid`='.$gaid);
			$alb = $db->loadObject();
			$db->disconnect();
			$this->_album = $alb;
		}
	}

}
