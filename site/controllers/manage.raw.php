<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2016 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

//require_once 'manage.php';

class MeedyaControllerManage extends JControllerLegacy
{
	protected $gallPath;
	protected $impacts = [];

	public function __construct ($config = array())
	{
		$this->gallPath = MeedyaHelper::userDataPath();
		if (RJC_DBUG) { MeedyaHelper::log('MeedyaControllerManageRaw'); }
		parent::__construct($config);
	}

	/* * * * * * * * * * functions for format=raw calls * * * * * * * * * */
	/*--------------------------------------------------------------------*/

	// task to receive and store uploaded files
	public function upfile ()
	{
		if (JDEBUG) { JLog::add('upfile: '.print_r($this->input, true), JLog::INFO, 'com_meedya'); }
	//	$galid = base64_decode($this->input->get('galid', '', 'base64'));
		$file = $this->input->files->get('userpicture');

		try {
			if (!$file) throw new Exception('Parameters error.');
			switch ($file['error']) {
				case UPLOAD_ERR_OK:
					break;
				case UPLOAD_ERR_NO_FILE:
					throw new Exception('No file sent.');
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					throw new Exception('Exceeded filesize limit.');
				default:
					throw new Exception('Unknown error.');
			}
			$m = $this->getModel('manage');
			$m->storeFile($file, $this->input->post->get('album', 0, 'int'));
		}
		catch (Exception $e) {
			header('HTTP/1.1 '.(400+$e->getCode()).' Failed to store file');
			echo 'Error storing file: ' . $e->getMessage();
		}
	}


	// task to create a new album
	public function newAlbum ()
	{
		if (JSession::checkToken()) {
			$a = $this->input->post->get('albnam', 'A NEW ALBUM', 'string');
			$p = $this->input->post->get('paralb', 0, 'int');
			$d = $this->input->post->get('albdesc', null, 'string');
			$m = $this->getModel('manage');
			$aid = $m->addAlbum($a, $p, $d);
			if (!$aid) {
				header("HTTP/1.0 400 Could not create album: {$a}");
			} elseif ($this->input->post->get('o', 0, 'int')) {
				$albs = $m->getAlbumsList();
				echo JHtml::_('meedya.albumsHierOptions', $albs, $aid);
			}
		} else {
			echo JText::_('JINVALID_TOKEN');
		}
	}


	// task to remove items from an album
	public function removeItems ()
	{
		if (JSession::checkToken()) {
			$aid = $this->input->post->get('aid','','int');
			$parm = $this->input->post->get('items','','string');
			$items = explode('|',$parm);
			$m = $this->getModel('manage');
			$m->removeItems($aid, $items);
		} else {
			echo JText::_('JINVALID_TOKEN');
		}
	}


	public function adjustAlbPaid ()
	{
		if (JSession::checkToken()) {
			$aid = $this->input->post->get('aid','','int');
			$paid = $this->input->post->get('paid','','int');
			$m = $this->getModel('manage');
			$m->setAlbumPaid($aid, $paid);
		} else {
			echo JText::_('JINVALID_TOKEN');
		}
	}


	public function impstps ()
	{
		$fld = $this->input->get('fld','','STRING');
		if ($fld) $this->impacts[] = array('act'=>'na','ttl'=>$fld);
		$this->buildImpActs('/import/'.($fld ? ($fld.'/') : ''));
		echo json_encode($this->impacts);
	}


	public function impact ()
	{
		$this->_log(print_r($this->input->post->getArray(), true));
		$m = $this->getModel('manage');
		$act = $this->input->post->get('act','','STRING');
		switch ($act) {
			case 'na':
				$ttl = $this->input->post->get('ttl','New Album','STRING');
				$pid = $this->input->post->get('pid',0,'INT');
				$aid = $m->addAlbum($ttl, $pid);
				echo json_encode(array('r'=>$aid,'tt'=>'new alb id'));
				break;
			case 'ii':
				$aid = $this->input->post->get('aid',null,'INT');
				$fp = $this->input->post->get('fp','','STRING');
				$fast = $this->input->post->get('fat','','BOOL');
				$this->placeImageFiles($fp, $aid, $fast);
				$iid = 9;		//$m->addImage($fp, $cid, $gid);
				echo json_encode(array('r'=>$iid,'aid'=>$aid,'tt'=>'new img id'));
				break;
			case 'pa';
				break;
		}
	}


	private function placeImageFiles ($fpath, $aid, $fast)
	{
		$this->_log(print_r(array($aid, $fpath), true));
		$dir = JPATH_BASE . '/' . $this->gallPath;

		$src = $dir . $fpath;
		$dst = $dir . '/img/';
		$pp = pathinfo($fpath);
		$n = 0; $u = '';
		while (file_exists($dst.$pp['filename'].$u.'.'.$pp['extension'])) {
			$u = '~'.$n++;
		}
		$fn = $pp['filename'].$u.'.'.$pp['extension'];
		$fdst = $dst.$fn;
		$this->_log(print_r(array($src, $fdst), true));
		if (copy($src, $fdst)) {
			$m = $this->getModel('manage');
			$m->processFile($fdst, $fn, $aid, $fast ? $pp['filename'] : null);
		}
	}


	private function buildImpActs ($dir='')
	{
		$aDir = $this->gallPath.$dir;
		$dh = opendir(rtrim($aDir,'/'));
		while (false !== ($file = readdir($dh))) {
			if ($file[0] != '.') {
				$fp = $dir.$file;
				if (is_dir($aDir.$file)) {
					$this->impacts[] = array('act'=>'na','ttl'=>$file);
					$this->buildImpActs($fp.'/');
				} else {
					// check here that it is a valid image file
					if ($this->validImageFile($aDir.$file)) $this->impacts[] = array('act'=>'ii','fp'=>$fp);
				}
			}
		}
		$this->impacts[] = array('act'=>'pa');
		closedir($dh);
	}

	private function validImageFile ($fpath)
	{
		$mtype = '';
		if (function_exists('finfo_open') && ($finf = finfo_open(FILEINFO_MIME_TYPE))) {
			$mtype = finfo_file($finf, $fpath);
			finfo_close($finf);
		}
		$mp = explode('/', $mtype);
		return (is_array($mp) && $mp[0] == 'image');
	}

	private function _log ($msg)
	{
		if (RJC_DBUG) { file_put_contents('ILOG.txt', $msg, FILE_APPEND); }
	}


}
