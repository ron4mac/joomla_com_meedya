<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2023 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.3.2
*/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Layout\LayoutHelper;

JLoader::register('JHtmlMeedya', JPATH_COMPONENT . '/helpers/html/meedya.php');

class MeedyaControllerManage extends JControllerLegacy
{
	protected $gallPath;
	protected $impacts = [];

	public function __construct ($config = [])
	{
		$this->gallPath = MeedyaHelper::userDataPath();
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
		$resp = [];
		ob_start();
		$uplodr_obj = new Up_Load($this->input, $toname, ['target_dir'=>JPATH_BASE.'/']);
		if ($toname) {
			$m = $this->getModel('manage');
			$qr = $m->storeFile($toname, $this->input->post, $uplodr_obj);
			$resp['qp'] = $qr;
		}
		$smsg = ob_get_contents();
		ob_end_clean();
		if ($smsg) {
			$resp['smsg'] = $smsg;
		}
		if ($resp) {
			echo json_encode($resp);
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

	// task to change the parent of an album
	public function adjustAlbPaid ()
	{
		$this->tokenCheck();
		$aid = $this->input->post->get('aid','','int');
		$paid = $this->input->post->get('paid','','int');
		$m = $this->getModel('manage');
		$m->setAlbumPaid($aid, $paid);
	}

	// get a select element to choose an album
	public function getAlbumSelect ()
	{
		$xc = $this->input->post->get('exc', '', 'anum');
		$exca = explode(',', $xc);
		$m = $this->getModel('manage');
		echo LayoutHelper::render('addtoalbum', ['albs'=>$m->getAlbumsList(), 'exca'=>$exca], JPATH_ROOT.'/components/com_meedya/layouts');
		return;

		$anp = $this->input->post->get('anp','a','word');
		switch ($anp) {
			case 'a':
				$lbl = 'COM_MEEDYA_ALBUM';
				$zo = '';
				break;
			case 'n':
				break;
			case 'p':
				$lbl = 'COM_MEEDYA_ALBUM_PARENT';
				$zo = '<option value="0">' . Text::_('COM_MEEDYA_H5U_NONE') . '</option>';
				break;
		}
		$m = $this->getModel('manage');
		$albs = $m->getAlbumsList();
		$body = '<div class="nualbtop">
		<dl>
		<dt><label for="h5u_palbum">' . Text::_($lbl) . '</label></dt>
		<dd>
			<select class="form-select form-select-sm" id="h5u_palbum" name="h5u_palbum">
				<!-- <option value="">' . Text::_('COM_MEEDYA_H5U_SELPAR') . '</option> -->
				' .$zo.'
				' . HTMLHelper::_('meedya.albumsHierOptions', $albs) . '
			</select>
		</dd>
		</dl>
		</div>';
		echo $body;
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
		echo '<div class="zoom-ctnr"><div class="zoom-closex" onclick="Meedya.Zoom.close(event)">X</div>';
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

	public function getItemInfo ()
	{
		$iid = $this->input->post->getInt('iid', 0);
		if (!$iid) return;
		$url = $this->gallPath;
		$m = $this->getModel('manage');
		$item = $m->getItem($iid);
		$mime = explode('/',$item['mtype']);
		echo '<div class="info-ctnr"><div class="info-closex" onclick="Meedya.itmInfo.close(event)">X</div><dl>';
		switch ($mime[0]) {
			case 'image':
				echo '<dt>TITLE</dt><dd>'.$item['title'].'</dd>';
				echo '<dt>DESCRIPTION</dt><dd>'.$item['desc'].'</dd>';
				echo '<dt>KEYWORDS</dt><dd>'.$item['kywrd'].'</dd>';
				echo '<dt>FILENAME</dt><dd>'.$item['file'].'</dd>';
				echo '<dt>MIMETYPE</dt><dd>'.$item['mtype'].'</dd>';
				echo '<dt>STORAGE</dt><dd>'. MeedyaHelper::formatBytes($item['tsize']).'</dd>';
				echo '<dt>UPLOAD</dt><dd>'.$item['timed'].'</dd>';
				echo '<dt>EXPOSURE</dt><dd>'.$item['expodt'].'</dd>';
				echo '<dt>ALBUMS</dt><dd>'.$m->getAlbumTitles($item['album']).'</dd>';
				echo '<dt>COMMENTS</dt><dd>'.$item['cmntcnt'].'</dd>';
				echo '<dt>GALLEREY_ID</dt><dd>'.$item['id'].'</dd>';
			//	echo'<xmp>';print_r($item);echo'</xmp>';
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
		echo '</dl></div>';
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
