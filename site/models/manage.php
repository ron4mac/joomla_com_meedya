<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2020 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

require_once __DIR__ . '/meedya.php';

//JLoader::register('MeedyaHelper', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/meedya.php');

class MeedyaModelManage extends MeedyaModelMeedya
{
	//protected $context = 'manage';
	protected $album = null;
	protected $ownid;

	public function __construct ($config = array())
	{
		// set filter fields for Search Tools purposes
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'album',
				'level',
				'tag'
			);
		}
		if (RJC_DBUG) { MeedyaHelper::log('MeedyaModelManage'); }
		parent::__construct($config);
		$this->ownid = JFactory::getUser()->get('id');
	}

	public function updateConfig ($type, $vals)
	{
		$db = $this->getDbo();
		$qvals = $db->quote(json_encode($vals));
		$typ = $db->quote($type);
		$db->setQuery('SELECT `vals` FROM `config` WHERE `type`='.$typ);
		$r = $db->loadResult();
		if ($r) {
			$q = 'UPDATE `config` SET `vals` = '.$qvals.' WHERE `type`='.$typ;
		} else {
			$q = 'INSERT INTO `config` (`type`,`vals`) VALUES ('.$typ.','.$qvals.')';
		}
		$db->setQuery($q);
		$db->execute();
	}

	public function getDbTime ()
	{
		$db = $this->getDbo();
		$db->setQuery('SELECT CURRENT_TIMESTAMP');
		$r = $db->loadResult();
		return $r;
	}

	public function getAlbum ($aid=0)
	{
		$aid = $aid ?: ($this->state->get('album.id') ?: 0);
		$db = $this->getDbo();
		$db->setQuery('SELECT * FROM `albums` WHERE `aid`='.$aid);
		$r = $db->loadAssoc();

		return $r;
	}

	public function setAlbumPaid ($aid, $paid)
	{
		$hord = $paid ? $this->getParentNextHord($paid) : $aid;
		$db = $this->getDbo();
		$db->setQuery('UPDATE `albums` SET `paid`='.$paid.', `hord`=\''.$hord.'\' WHERE `aid`='.$aid);
		$db->execute();
	}

	public function getAllAlbums ()
	{
		$db = $this->getDbo();
		$db->setQuery('SELECT aid,paid,hord,title,desc,items FROM `albums` ORDER BY albhier(aid,paid)');
		$albs = $db->loadAssocList();
		return $albs;
	}

	public function removeItems ($aid, $list)
	{
		if (is_null($this->album) || $this->album['aid']!=$aid) $this->album = $this->getAlbum($aid);
		$cur = explode('|', $this->album['items']);
		$mod = array_diff($cur, $list);
		$items = '\''.implode('|',$mod).'\'';
		$this->updateAlbum(array('items'=>$items));
	}

	public function addItems2Album ($items, $album)
	{
		if (RJC_DBUG) { MeedyaHelper::log('addItems ... album: '.$album.' items: '.print_r($items,true)); }
		$db = $this->getDbo();
		$db->transactionStart();
		$db->setQuery('SELECT `items` FROM `albums` WHERE `aid`='.$album);
		$r = $db->loadResult();
		if ($r!==false || is_null($r)) {
			$cur = $r ? explode('|', $r) : array();
			$diff = array_diff($items, $cur);
			$items = array_merge($cur, $diff);
			$q = 'UPDATE `albums` SET `items`=\''.implode('|',$items).'\' WHERE `aid`='.$album;
			$db->setQuery($q);
			$db->execute();
		} else {
			$q = 'INSERT INTO albums (items,ownid,title,tstamp) VALUES ('.$db->quote(implode('|',$items)).','.$this->ownid.',\'New Album\','.time().')';
			$db->setQuery($q);
			$db->execute();
			$album = $db->insertid();
		}
		$db->transactionCommit();
		$this->addAlbum2Items($album, $items);
	}

	public function addAlbum2Items ($album, $items)
	{
		if (!is_array($album)) $album = array($album);
		$db = $this->getDbo();
		$db->transactionStart();
		$db->setQuery('SELECT `id`,`album` FROM `meedyaitems` WHERE `id` IN ('.implode(',',$items).')');
		$itms = $db->loadAssocList();
		foreach ($itms as $itm) {
			$cur = explode('|',$itm['album']);
			$albs = $this->uniqVals(array_merge($cur, $album));
			$q = 'UPDATE `meedyaitems` SET `album`=\''.implode('|',$albs).'\' WHERE `id`='.$itm['id'];
			$db->setQuery($q);
			$db->execute();
		}
		$db->transactionCommit();
	}

	public function addItem ($fnam, $mtype, $ittl, $albm, $fsize, $tsize, $xpdt)
	{
		$db = $this->getDbo();
		$flds = $db->quoteName(array('file','mtype','ownid','title','album','fsize','tsize','expodt'));
		$vals = $db->quote(array($fnam, $mtype, $this->ownid, $ittl, $albm, (int)$fsize, (int)$tsize, $xpdt));
		if (count($vals) < 7) $vals[] = 'NULL';
		$db->setQuery('INSERT INTO `meedyaitems` ('.implode(',', $flds).') VALUES ('.implode(',', $vals).')');
		if (RJC_DBUG) { MeedyaHelper::log('addItem: '.$db->getQuery()); }
		try {
			$db->execute();
			$iid = $db->insertid();
			$this->addItems2Album((array)$iid, $albm);
		} catch (RuntimeException $e) {
			if (RJC_DBUG) { MeedyaHelper::log('addItem error: '.$e->getMessage()); }
			JError::raiseError(500, $e->getMessage());
		}
	}

	public function addAlbum ($anam, $parid=0, $desc='')
	{
		$db = $this->getDbo();
		if ($parid) {
			$hord = $this->getParentNextHord($parid);
			$q = 'INSERT INTO albums (`title`,`desc`,`paid`,`hord`,`ownid`,`tstamp`) VALUES ('.$db->quote($anam).','.$db->quote($desc).','.$parid.','.$db->quote($hord).','.$this->ownid.','.time().')';
		} else {
			$q = 'INSERT INTO `albums` (`title`,`desc`,`ownid`,`tstamp`) VALUES ('.$db->quote($anam).','.$db->quote($desc).','.$this->ownid.','.time().')';
		}
		$db->setQuery($q);
		$db->execute();
		$rowid = $db->insertid();
		if (!$parid) {
			$db->setQuery('UPDATE `albums` SET `hord`='.$db->quote($rowid).' WHERE `aid`='.$rowid);
			$db->execute();
		}
		return $rowid;
	}

	public function getImages ($parm)
	{
		if (is_array($parm)) {
			$where = '`id` IN ('.implode(',',$parm).')';
		} else {
			$where = '`timed` > "'.$parm.'"';
		}
		$db = $this->getDbo();
		$db->setQuery('SELECT * FROM `meedyaitems` WHERE ' . $where);
		$itms = $db->loadObjectList();
		return $itms;
	}

	public function updImage ($iid, $vals)
	{
		$db = $this->getDbo();
		$sets = array();
		foreach ($vals as $k=>$v) {
			array_push($sets, '`'.$k.'` = '. $db->quote($v));
		}
		$db->setQuery('UPDATE `meedyaitems` SET '.implode(', ', $sets).' WHERE `id`=' . $iid);
		$db->execute();
	}

	public function storeFile ($file, $alb, $impath='')
	{
		if (RJC_DBUG) { MeedyaHelper::log("store ... album: {$alb} file: {$file['name']}"); }

		$params = JFactory::getApplication()->getParams();
		$keep = (int)$params->get('keep_orig', 0);
		$quota = MeedyaHelper::getStoreQuota($params);
		if ($quota) {
			if ($this->getStorageTotal() > $quota) throw new Exception('Quota exceeded', 3);
		}
//		$mtype = '';
//		if (function_exists('finfo_open') && ($finf = finfo_open(FILEINFO_MIME_TYPE))) {
//			$mtype = finfo_file($finf, $file['tmp_name']);
//			finfo_close($finf);
//		}

		$mdydir = realpath(MeedyaHelper::userDataPath());
		$fPath = $mdydir.'/img/';

		//preg_match('/(.+)\.(.*?)\Z/', $file['name'], $matches);
		//$nr = 0;
		//$base_name = $matches[1];
		//$uniq = '';
		//$ext = '.' . $matches[2];

		$fnp = pathinfo($file['name']);
		$base_name = $fnp['filename'];
		$ext = isset($fnp['extension']) ? ('.'.$fnp['extension']) : '';
		$uniq = '';
		$nr = 0;

		while (file_exists($fPath.$base_name.$uniq.$ext)) {
			$uniq = '~'.$nr++;
		}
		$ffpnam = $fPath.$base_name.$uniq.$ext;

		if ($impath) {
			if (!rename($impath.$file['name'], $ffpnam)) {
				return false;
			}
		} else {
			if (!(move_uploaded_file($file['tmp_name'], $ffpnam))) {
				throw new Exception('Could not place file');
			}
		}

/*		$fsize = filesize($ffpnam);
		$xpdt = null;
		$xf = @exif_read_data($ffpnam, 'IFD0,EXIF', true);
		if (RJC_DBUG) { MeedyaHelper::log('exif: '.print_r($xf,true)); }
		if (isset($xf['EXIF']['DateTimeOriginal'])) {
			if ($xf['EXIF']['DateTimeOriginal'] != '0000:00:00 00:00:00') {
				$xpdt = $xf['EXIF']['DateTimeOriginal'];
			}
		} elseif (isset($xf['IFD0']['DateTime'])) {
			if ($xf['IFD0']['DateTime'] != '0000:00:00 00:00:00') {
				$xpdt = $xf['IFD0']['DateTime'];
			}
		}
		$imgP = MeedyaHelper::getImgProc($ffpnam);
		if (RJC_DBUG && $imgP->getErrors()) { MeedyaHelper::log(implode("\n",$imgP->getErrors())); }
		$xsize = $imgP->orientImage($ffpnam);
		$xsize += $imgP->createMedium($mdydir.'/med/'.$base_name.$uniq, $ext);
		$xsize += $imgP->createThumb($mdydir.'/thm/'.$base_name.$uniq, $ext);	*/
		$ittl = isset($file['title']) ? $file['title'] : null;
	//	$this->addItem($base_name.$uniq.$ext, $mtype, $ittl, $alb, $fsize, $fsize+$xsize, $xpdt);
		$this->processFile($ffpnam, $base_name.$uniq.$ext, $alb, $ittl, $keep);
		if (!$keep) {
			@unlink($ffpnam);
		}
	}

	public function processFile ($fpath, $fname, $alb, $ittl, $keep=false)
	{
		$mtype = '';
		if (function_exists('finfo_open') && ($finf = finfo_open(FILEINFO_MIME_TYPE))) {
			$mtype = finfo_file($finf, $fpath);
			finfo_close($finf);
		}
		$fsize = $keep ? filesize($fpath) : 0;
		$xpdt = null;
		$xf = @exif_read_data($fpath, 'IFD0,EXIF', true);
		if (RJC_DBUG) { MeedyaHelper::log('exif: '.print_r($xf,true)); }
		if (isset($xf['EXIF']['DateTimeOriginal'])) {
			if ($xf['EXIF']['DateTimeOriginal'] != '0000:00:00 00:00:00') {
				$xpdt = $xf['EXIF']['DateTimeOriginal'];
			}
		} elseif (isset($xf['IFD0']['DateTime'])) {
			if ($xf['IFD0']['DateTime'] != '0000:00:00 00:00:00') {
				$xpdt = $xf['IFD0']['DateTime'];
			}
		}
		$imgP = MeedyaHelper::getImgProc($fpath);
		if (RJC_DBUG && $imgP->getErrors()) { MeedyaHelper::log(implode("\n",$imgP->getErrors())); }
		$mdydir = realpath(MeedyaHelper::userDataPath());
		$fnp = pathinfo($fname);
		$base_name = $fnp['filename'];
		$ext = isset($fnp['extension']) ? ('.'.$fnp['extension']) : '';
		$xsize = $imgP->orientImage($fpath);
		if (!$keep) $xsize = 0;
		$xsize += $imgP->createMedium($mdydir.'/med/'.$base_name, $ext);
		$xsize += $imgP->createThumb($mdydir.'/thm/'.$base_name, $ext);
		$this->addItem($fname, $mtype, $ittl, $alb, $fsize, $fsize+$xsize, $xpdt);
		if (!$keep) @unlink($fpath);
	}

	public function getStorageTotal ()
	{
		$db = $this->getDbo();
		$db->setQuery('SELECT SUM(`tsize`) FROM `meedyaitems`');
		$r = $db->loadResult();
		return (is_null($r) ? 0 : $r) + filesize(MeedyaHelper::userDataPath().'/meedya.db3');
	}

	// remove items from storage and from the Database
	// $itms - array of item id numbers
	// $igna - album id number to ignore because it will be removed anyway
	public function deleteItems ($itms, $igna=0)
	{
//		require_once JPATH_COMPONENT.'/helpers/meedya.php';
		$mdydir = MeedyaHelper::userDataPath();
		$db = $this->getDbo();
		foreach ($itms as $itm) {
			//remove files
			$db->setQuery('SELECT `file`,`album` FROM `meedyaitems` WHERE `id`='.$itm);
			$r = $db->loadAssoc();
			@unlink($mdydir.'/img/'.$r['file']);
			@unlink($mdydir.'/thm/'.$r['file']);
			@unlink($mdydir.'/med/'.$r['file']);
			$this->removeItemFromAlbums($itm, $r['album'], $igna);
		}
		$db->setQuery('DELETE FROM `meedyaitems` WHERE `id` IN ('.implode(',',$itms).')');
		$db->execute();
	}

	// remove albums from the Database
	// $albs - array of album id numbers
	// $wipe - also remove all items in the album
	public function removeAlbums ($albs, $wipe=false)
	{
		$db = $this->getDbo();
		if ($wipe) {
			foreach ($albs as $alb) {
				$db->setQuery('SELECT `items` FROM `albums` WHERE `aid`='.$alb);
				$r = $db->loadResult();
				$itms = $r ? explode('|',$r) : array();
				$this->deleteItems($itms, $alb);
			}
		}
		$db->setQuery('DELETE FROM `albums` WHERE `aid` IN ('.implode(',',$albs).')');
		$db->execute();
	}

	// remove an item from albums that reference it (because it is being deleted)
	// $itm - item id
	// $astr - pipe char separated string of album ids
	// $igna - album id number to ignore because it will be removed anyway
	private function removeItemFromAlbums ($itm, $astr, $igna)
	{
		$db = $this->getDbo();
		$albs = explode('|',$astr);
		foreach ($albs as $alb) {
			if ($alb == $igna) continue;
			$db->setQuery('SELECT `items`,`thumb` FROM `albums` WHERE `aid`='.$alb);
			$r = $db->loadAssoc();
			if (!$r) continue;
			$itms = explode('|',$r['items']);
			$itms = array_diff($itms, array($itm));
			$istr = implode('|',$itms);
			if ($r['thumb'] == $itm) $r['thumb'] = 0;
			$db->setQuery('UPDATE `albums` SET `items`=\''.$istr.'\', `thumb`='.$r['thumb'].' WHERE `aid`='.$alb);
			if (RJC_DBUG) { MeedyaHelper::log('removeItemFromAlbums: '.$db->getQuery()); }
			try {
				$db->execute();
			} catch (RuntimeException $e) {
				if (RJC_DBUG) { MeedyaHelper::log('removeItemFromAlbums error: '.$e->getMessage()); }
				JError::raiseError(500, $e->getMessage());
			}
		}
	}

	public function saveAlbum ($aid, $flds)
	{	//echo'<xmp>';var_dump($aid, $flds);echo'</xmp>';jexit();
		if (is_null($this->album) || $this->album['aid']!=$aid) $this->album = $this->getAlbum($aid);
	//	$this->album = array('aid'=>$aid);
	// @@need to remove album id from removed items
	//	echo'<xmp>';var_dump($this->album['items'], $flds['items']);echo'</xmp>';jexit();
// @@@@@@@@@@ use a different method to determine what to remove rather than a dangerous diff of items
		$rmvd = array_diff(explode('|', $this->album['items']), explode('|', $flds['items']));
		foreach ($rmvd as $itm) {
			$this->removeItemAlbum($itm, $aid);
		}
		$this->updateAlbum($flds);
	}

	private function updateAlbum ($fields)
	{
		if (is_null($this->album)) $this->album = $this->getAlbum();
		$db = $this->getDbo();
		$sets = '';
		foreach ($fields as $k=>$v) {
			if ($sets) $sets .= ', ';
			$sets .= $k.' = '.$db->quote($v);
		}
		$db->setQuery('UPDATE `albums` SET '.$sets.' WHERE `aid`='.$this->album['aid']);
	//	echo $db->getQuery();
		$db->execute();
	}

	private function removeItemAlbum ($itm, $alb)
	{
		$db = $this->getDbo();
		$db->setQuery('SELECT `album` FROM `meedyaitems` WHERE `id`='.$itm);
		$albs = explode('|', $db->loadResult());
		$albs = array_diff($albs, array($alb));
		$db->setQuery('UPDATE `meedyaitems` SET `album`=\''.implode('|', $albs).'\' WHERE `id`=' . $itm);
		$db->execute();
	}

	private function getParentNextHord ($parid)
	{
		$db = $this->getDbo();
		$db->setQuery('SELECT `hord` FROM `albums` WHERE `aid`='.$parid);
		$phord = $db->loadResult();
		$lvl = substr_count($phord, '.') + 1;
		$db->setQuery('SELECT `aid`,`hord` FROM `albums` WHERE `hord` LIKE "'.$phord.'.%"');
		$fams = $db->loadAssocList();
		$v = 1;
		foreach ($fams as $fam) {
			$hs = explode('.', $fam['hord']);
			if ($hs[$lvl] >= $v) {
				$v = $hs[$lvl] + 1;
			}
		}
		return $phord . '.' . $v;
	}

	protected function populateState ($ordering = null, $direction = null)
	{	//echo'####POPSTATE####';
		// Initialise variables.
		$app = JFactory::getApplication();
		$params = JComponentHelper::getParams('com_meedya');
		$input = $app->input;

		if (RJC_DBUG) { MeedyaHelper::log('populateState', $input); }

		// album ID
		$aid = $input->get('album', 0, 'INT');
		if (!$aid) { $aid = $input->get('aid', 0, 'INT'); }
		$this->state->set('album.id', $aid);	//echo'<xmp>';var_dump($this->state);echo'</xmp>';


		// List state information
//		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'uint');
//		$this->setState('list.limit', $limit);

//		$limitstart = JRequest::getUInt('limitstart', 0);
//		$this->setState('list.start', $limitstart);

		// List state information
//		$value = $app->input->get('limit', $app->get('list_limit', 0), 'uint');
//		$this->setState('list.limit', $value);

//		$value = $app->input->get('start', 0, 'uint');
//		$this->setState('list.start', $value);

		// List state information
	//	$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
	//	$this->setState('list.limit'.$aid, $limit);

	//	$limitstart = $input->getInt('limitstart', 0);
	//	$this->setState('list.start'.$aid, $limitstart);

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$tag = $this->getUserStateFromRequest($this->context . '.filter.tag', 'filter_tag', '');
		$this->setState('filter.tag', $tag);

		// Load the parameters.
		$this->setState('params', $params);

		parent::populateState($ordering, $direction);
	}

	protected function getListQuery ()
	{
		if ($this->state->get('album.id', 0) || $this->filterFormName == 'filter_images') {
			return $this->itemsListQuery();
		} else {
			return $this->albumsListQuery();
		}
		echo $this->context;
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('albums');
	//	$query->from('meedyaitems');
	//	$aid = $this->state->get('filter.album', 0);
	//	if ($aid) {
	//		$query->where('album='.$aid);
	//	}
	//	$query->order('expodt');
	//	echo $query,'<xmp>';var_dump($this->state);echo'</xmp>';
		return $query;
	}

	protected function __getListQuery ()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
	//	$query->from('albums');
		$query->from('meedyaitems');
		$aid = $this->state->get('filter.album', 0);
		if ($aid) {
			if ($aid < 0) {
				$query->where('album IS NULL OR album=\'\'');
			} else {
				$query->where('album='.$aid);
			}
		}
		$query->order('expodt');
	//	echo $query,'<xmp>';var_dump($this->state);echo'</xmp>';
		return $query;
	}

	private function albumsListQuery ()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('albums');
		if (RJC_DBUG) { MeedyaHelper::log('ModelManage getListQuery(albums)', $query); }
		return $query;
	}

	private function itemsListQuery ()
	{
		//echo '<xmp>';var_dump($this->state);echo'</xmp>';
		if ($this->filterFormName !== 'filter_images') {
			$db = $this->getDbo();
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
			if (RJC_DBUG) { MeedyaHelper::log('ModelManage getListQuery(items)', $query); }
			return $query;
		}

		$db = $this->getDbo();
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

	private function uniqVals (array $ary)
	{
		$vals = []; 
		foreach ($ary as $k=>$v) {    
			$vals[$v] = true; 
		} 
		return array_keys($vals); 
	}

}
