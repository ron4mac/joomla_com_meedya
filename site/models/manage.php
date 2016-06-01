<?php
defined('_JEXEC') or die;

require_once __DIR__ . '/meedya.php';

JLoader::register('MeedyaHelper', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/meedya.php');

class MeedyaModelManage extends MeedyaModelMeedya
{
	protected $album = null;

	public function updateConfig ($type, $vals)
	{
	//	$db = parent::getDBO();
		$db = $this->getDbo();
		$qvals = $db->quote(serialize($vals));
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
	//	$db = parent::getDBO();
		$db = $this->getDbo();
		$db->setQuery('SELECT CURRENT_TIMESTAMP');
		$r = $db->loadResult();
		return $r;
	}

	public function getAlbum ($aid=0)
	{
		$aid = $aid ?: ($this->state->get('album.id') ?: 0);
	//	$db = parent::getDBO();
		$db = $this->getDbo();
		$db->setQuery('SELECT * FROM `albums` WHERE `aid`='.$aid);
		$r = $db->loadAssoc();

		return $r;
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
		if (JDEBUG) { JLog::add('addItems ... album: '.$album.' items: '.print_r($items,true), JLog::INFO, 'com_meedya'); }
	//	$db = parent::getDBO();
		$db = $this->getDbo();
		$db->transactionStart();
		$db->setQuery('SELECT `items` FROM `albums` WHERE `aid`='.$album);
		$r = $db->loadResult();
		if ($r || is_null($r)) {
			$cur = $r ? explode('|',$r) : array();
			$diff = array_diff($items, $cur);
			$items = array_merge($cur,$diff);
			$q = 'UPDATE `albums` SET `items`=\''.implode('|',$items).'\' WHERE `aid`='.$album;
		} else {
			$q = 'INSERT INTO `albums` ("items","title") VALUES (\''.implode('|',$items).'\',\'New Album\')';
		}
		$db->setQuery($q);
		$db->execute();
		$db->transactionCommit();
	}

	public function addPicture ($fnam, $albm, $fsize, $tsize)
	{
	//	$db = parent::getDBO();
		$db = $this->getDbo();
		$fnam = $db->escape($fnam);
		$db->setQuery('INSERT INTO `meedyaitems` ("file","album","fsize","tsize") VALUES (\''.$fnam.'\',\''.$albm.'\',\''.$fsize.'\',\''.$tsize.'\')');
		$db->execute();
		$iid = $db->insertid();

		$this->addItems2Album((array)$iid, $albm);
	}

	public function addAlbum ($anam, $parid, $desc='')
	{
	//	$db = parent::getDBO();
		$db = $this->getDbo();
		if ($parid) {
			$hord = $this->getParentNextHord($parid);
			$q = 'INSERT INTO `albums` (`title`,`desc`,`paid`,`hord`) VALUES ('.$db->quote($anam).','.$db->quote($desc).','.$parid.','.$db->quote($hord).')';
		} else {
			$q = 'INSERT INTO `albums` (`title`,`desc`) VALUES ('.$db->quote($anam).','.$db->quote($desc).')';
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
	{	//var_dump($parm);
		if (is_array($parm)) {
			$where = '`id` IN ('.implode(',',$parm).')';
		} else {
			$where = '`timed` > "'.$parm.'"';
		}
	//	$db = parent::getDBO();
		$db = $this->getDbo();
		$db->setQuery('SELECT * FROM `meedyaitems` WHERE ' . $where);
		$itms = $db->loadAssocList();
		return $itms;
	}

	public function updImage ($iid, $vals)
	{
	//	$db = parent::getDBO();
		$db = $this->getDbo();
		$sets = array();
		foreach ($vals as $k=>$v) {
			array_push($sets, '`'.$k.'` = '. $db->quote($v));
		}
		$db->setQuery('UPDATE `meedyaitems` SET '.implode(', ', $sets).' WHERE `id`=' . $iid);
		$db->execute();
	}

	public function storeFile ($file, $alb)
	{
		if (JDEBUG) { JLog::add("store ... album: {$alb} file: {$file['name']}", JLog::INFO, 'com_meedya'); }
//		require_once JPATH_COMPONENT.'/helpers/meedya.php';
		$mdydir = MeedyaHelper::userDataPath();
		$fPath = $mdydir.'/img/';

		preg_match('/(.+)\.(.*?)\Z/', $file['name'], $matches);
		$nr = 0;
		$base_name = $matches[1];
		$uniq = '';
		$ext = '.' . $matches[2];
		while (file_exists($fPath.$base_name.$uniq.$ext)) {
			$uniq = '~'.$nr++;
		}
		$ffpnam = $fPath.$base_name.$uniq.$ext;
		if (!(move_uploaded_file($file['tmp_name'], $ffpnam))) {
			header("HTTP/1.0 403 Could not place file ".$ffpnam);
			jexit();
		}
		$fsize = filesize($ffpnam);
		require_once JPATH_COMPONENT.'/helpers/graphicim.php';
	//	$xsize = MeedyaHelperGraphics::orientImage($ffpnam, $ffpnam);
	//	$xsize += MeedyaHelperGraphics::createThumb($ffpnam, $mdydir.'/thm/'.$base_name.$uniq, $ext);
	//	$xsize += MeedyaHelperGraphics::createMedium($ffpnam, $mdydir.'/med/'.$base_name.$uniq, $ext);
		$imgP = new ImageProcessor($ffpnam);
		$xsize = $imgP->orientImage($ffpnam);
		$xsize += $imgP->createMedium($mdydir.'/med/'.$base_name.$uniq, $ext);
		$xsize += $imgP->createThumb($mdydir.'/thm/'.$base_name.$uniq, $ext);
		$this->addPicture($base_name.$uniq.$ext, $alb, $fsize, $fsize+$xsize);
	}

	public function getStorageTotal ()
	{
	//	$db = parent::getDBO();
		$db = $this->getDbo();
		$db->setQuery('SELECT SUM(`tsize`) FROM `meedyaitems`');
		$r = $db->loadResult();
		return is_null($r) ? 0 : $r;
	}

	// remove items from storage and from the Database
	// $itms - array of item id numbers
	// $igna - album id number to ignore because it will be removed anyway
	public function deleteItems ($itms, $igna=0)
	{
//		require_once JPATH_COMPONENT.'/helpers/meedya.php';
		$mdydir = MeedyaHelper::userDataPath();

	//	$db = parent::getDBO();
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
	//	$db = parent::getDBO();
		$db = $this->getDbo();
		if ($wipe) {
			foreach ($albs as $alb) {
				$db->setQuery('SELECT `items` FROM `albums` WHERE `aid`='.$alb);
				$r = $db->loadResult();
				$itms = $r ? explode('|',$r) : array();
				$this->deleteItems($itms);
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
	//	$db = parent::getDBO();
		$db = $this->getDbo();
		$albs = explode('|',$astr);
		foreach ($albs as $alb) {
			if ($alb == $igna) continue;
			$db->setQuery('SELECT `items`,`thumb` FROM `albums` WHERE `aid`='.$alb);
			$r = $db->loadAssoc();
			$itms = explode('|',$r['items']);
			$itms = array_diff($itms, array($itm));
			$istr = implode('|',$itms);
			if ($r['thumb'] == $itm) $r['thumb'] = 0;
			$db->setQuery('UPDATE `albums` SET `items`=\''.$istr.'\', `thumb`='.$r['thumb'].' WHERE `aid`='.$alb);
			$db->execute();
		}
	}

	private function updateAlbum ($fields)
	{
		if (is_null($this->album)) $this->album = $this->getAlbum();
		$sets = '';
		foreach ($fields as $k=>$v) {
			if ($sets) $sets .= ', ';
			$sets .= $k.' = '.$v;
		}
	//	$db = parent::getDBO();
		$db = $this->getDbo();
		$db->setQuery('UPDATE `albums` SET '.$sets.' WHERE `aid`='.$this->album['aid']);
		//echo $db->getQuery();
		$db->execute();
	}

	private function getParentNextHord ($parid)
	{
	//	$db = parent::getDBO();
		$db = $this->getDbo();
		$db->setQuery('SELECT `hord` FROM `albums` WHERE `aid`='.$parid);
		$phord = $db->loadResult();
		$lvl = substr_count($phord, '.') + 1;
		$db->setQuery('SELECT `aid`,`hord` FROM `albums` WHERE `hord` LIKE "'.$phord.'%"');
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

		// album ID
		$aid = $input->get('aid', 0, 'INT');
		$this->state->set('album.id', $aid);	//echo'<xmp>';var_dump($this->state);echo'</xmp>';

		// List state information
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		$this->setState('list.limit'.$aid, $limit);

		$limitstart = $input->getInt('limitstart', 0);
		$this->setState('list.start'.$aid, $limitstart);

		// Load the parameters.
		$this->setState('params', $params);
	}

	protected function getListQuery ()
	{
	//	$db = parent::getDBO();
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('albums');
		return $query;
	}

}