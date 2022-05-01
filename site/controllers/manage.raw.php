<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;

JLoader::register('JHtmlMeedya', JPATH_COMPONENT . '/helpers/html/meedya.php');

class MeedyaControllerManage extends JControllerLegacy
{
	protected $gallPath;
	protected $impacts = [];

	public function __construct ($config = [])
	{
		$this->gallPath = MeedyaHelper::userDataPath();
	//	if (RJC_DBUG) MeedyaHelper::log('MeedyaControllerManageRaw');
		parent::__construct($config);
	}

	/* * * * * * * * * * functions for format=raw calls * * * * * * * * * */
	/*--------------------------------------------------------------------*/

	// task to receive and store uploaded files
	public function upfile ()
	{
	//	if (RJC_DBUG) MeedyaHelper::log('upfile:', $this->input);
		$this->tokenCheck();

		require_once JPATH_COMPONENT.'/classes/uplodr.php';
		$toname = null;
		$uplodr_obj = new Up_Load($this->input, $toname, ['target_dir'=>JPATH_BASE.'/']);
		if ($toname) {
			$m = $this->getModel('manage');
			$qr = $m->storeFile($toname, $this->input->post, $uplodr_obj);
			echo ':qp:'.$qr;
		}
	}

	// task to create a new album
	public function newAlbum ()
	{
		$this->tokenCheck();
		$a = $this->input->post->get('albnam', 'A NEW ALBUM', 'string');
		$p = $this->input->post->get('paralb', 0, 'int');
		$d = $this->input->post->get('albdesc', null, 'string');
		$m = $this->getModel('manage');
		$aid = $m->addAlbum($a, $p, $d);
		if (!$aid) {
			header("HTTP/1.0 400 Could not create album: {$a}");
		} elseif ($this->input->post->get('o', 0, 'int')) {
			$albs = $m->getAlbumsList();
			echo HTMLHelper::_('meedya.albumsHierOptions', $albs, $aid);
		}
	}

/*	// task to remove items from an album
	public function removeItems ()
	{
		$this->tokenCheck();
		$aid = $this->input->post->get('aid','','int');
		$parm = $this->input->post->get('items','','string');
		$items = explode('|',$parm);
		$m = $this->getModel('manage');
		$m->removeItems($aid, $items);
	}
*/
	// task to change the parent of an album
	public function adjustAlbPaid ()
	{
		$this->tokenCheck();
		$aid = $this->input->post->get('aid','','int');
		$paid = $this->input->post->get('paid','','int');
		$m = $this->getModel('manage');
		$m->setAlbumPaid($aid, $paid);
	}

	public function impstps ()
	{
		$fld = $this->input->get('fld','','STRING');
		if ($fld) $this->impacts[] = ['act'=>'na','ttl'=>$fld];
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
				echo json_encode(['r'=>$aid,'tt'=>'new alb id']);
				break;
			case 'ii':
				$aid = $this->input->post->get('aid',null,'INT');
				$fp = $this->input->post->get('fp','','STRING');
				$fast = $this->input->post->get('fat','','BOOL');
				$this->placeImageFiles($fp, $aid, $fast);
				$iid = 9;		//$m->addImage($fp, $cid, $gid);
				echo json_encode(['r'=>$iid,'aid'=>$aid,'tt'=>'new img id']);
				break;
			case 'pa';
				break;
		}
	}

	public function getZoomItem ()
	{
		$iid = $this->input->post->getInt('iid', 0);
		if (!$iid) return;
		$url = $this->gallPath;
		$m = $this->getModel('manage');
		$item = $m->getItem($iid);
		$mime = explode('/',$item['mtype']);
		echo '<div class="zoom-ctnr"><div class="zoom-closex" onclick="iZoomClose(event)">X</div>';
		switch ($mime[0]) {
			case 'image':
				echo '<img class="eclipse" src="components/com_meedya/static/img/eclipse.svg" /><img class="zoom-zimg" src="'.$url.'/med/'.$item['file'].'" onload="this.previousSibling.style.opacity=0;this.style.opacity=1" />';
				break;
			case 'video':
				//return '<video class="zoom-zvid" autoplay><source src="'.$url.'" type="'.$ftyp['mime'].'"></video>';
				echo '<div><button class="btn btn-sm btn-secondary" onclick="Meedya.setVideoThumb(event,'.$iid.')">'.Text::_('COM_MEEDYA_SETVIDTHM').'</button></div>';
				echo '<div class="zoom-vid"><video class="zoom-zvid" id="zoom-zvid" controls autoplay><source src="'.$url.'/img/'.$item['file'].'"></video></div>';
				echo '<div style="display:none"><canvas id="my-video-canvas"></canvas><img id="my-vidover" src="components/com_meedya/static/img/vidovero.png" /></div>';
				break;
			default:
				echo '<div style="color:white">UNSUPPORTED FILE TYPE #'.$item['mtype'].'# '.$item['file'].'</div>';
		}
		echo '</div>';
	}

	public function setVideoThumb ()
	{
		$this->tokenCheck();
		if (empty($_POST['imgBase64'])) $this->fail('FAILED: server received no image data');
		$mtch = [];
		if (!preg_match('#^data:image/(\w*);base64,#', $_POST['imgBase64'], $mtch)) $this->fail('FAILED: could not parse image data');
		if ($mtch[1]=='jpg') $mtch[1] = 'jpeg';
		$tdir = JPATH_BASE . '/' . $this->gallPath . '/thm/';
		$iid = $this->input->post->getInt('vid', 0);
		$fn = 'videothumb_'.$iid.'.'.$mtch[1];
		if (!file_put_contents($tdir.$fn, base64_decode(substr($_POST['imgBase64'], strlen($mtch[0]))))) $this->fail('FAILED: could not save thumbnail');

		// set the item's thumb file in the db
		$m = $this->getModel('manage');
		$m->setItemThumb($iid, $fn);

		// return an image source path to the client
		echo JUri::root(true).'/'.$this->gallPath.'/thm/'.$fn;
	}


	private function placeImageFiles ($fpath, $aid, $fast)
	{
		$this->_log(print_r([$aid, $fpath], true));
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
		$this->_log(print_r([$src, $fdst]), true);
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
					$this->impacts[] = ['act'=>'na','ttl'=>$file];
					$this->buildImpActs($fp.'/');
				} else {
					// check here that it is a valid image file
					if ($this->validImageFile($aDir.$file)) $this->impacts[] = ['act'=>'ii','fp'=>$fp];
				}
			}
		}
		$this->impacts[] = ['act'=>'pa'];
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

	private function tokenCheck ()
	{
		if (!Session::checkToken()) {
			header('HTTP/1.1 403 Not Allowed');
			jexit(Text::_('JINVALID_TOKEN'));
		}
	}

	private function fail ($msg)
	{
		header('HTTP/1.1 400 Failure');
		jexit($msg);
	}

	private function _log ($msg)
	{
		if (RJC_DBUG) file_put_contents('ILOG.txt', $msg, FILE_APPEND);
	}


}
