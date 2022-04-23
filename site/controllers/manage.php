<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;

JLoader::register('JHtmlMeedya', JPATH_COMPONENT . '/helpers/html/meedya.php');

class MeedyaControllerManage extends JControllerLegacy
{
	protected $default_view = 'manage';
	protected $mnuItm;

	public function __construct ($config = [])
	{
	//	if (RJC_DBUG) MeedyaHelper::log('MeedyaControllerManage');
		parent::__construct($config);
		$this->mnuItm = $this->input->getInt('Itemid', 0);
	}


	public function display ($cachable = false, $urlparams = false)
	{
	//	if (RJC_DBUG) MeedyaHelper::log('MeedyaControllerManage : display');
		if ($this->nope()) return;
		$view = $this->getView('manage','html');
		$view->itemId = $this->mnuItm;
		return parent::display($cachable, $urlparams);
	}


	// display a screen/form for editing an album
	public function editAlbum ()
	{
		if ($this->nope()) return;
		$view = $this->getView('manage','html');
		$view->setLayout('albedit');
		$view->setModel($this->getModel('manage'), true);
		$view->referer = $this->input->server->getRaw('HTTP_REFERER');
		$view->display();
	}


/*	public function upload ()
	{
		$this->input->set('view', 'upload');
	}
*/

	// display a screen/form for uploading items
	public function doUpload ()
	{
		if ($this->nope()) return;
		$view = $this->getView('manage','html');
		$m = $this->getModel('manage');
		$view->aid = $this->input->get->get('aid',0,'int');
		$view->albums = $m->getAlbumsList();
		$view->dbTime = $m->getDbTime();
		$view->totStore = (int)$m->getStorageTotal();
		$view->itemId = $this->mnuItm;
		$view->setLayout('upload');
		$view->display();
	}


	// display screen(s) of all items for editing purposes
	public function editImgs ()
	{
		if ($this->nope()) return;
		$view = $this->getView('manage','html');
		$view->itemId = $this->mnuItm;
		$view->setLayout('images');
		$m = $this->getModel('manage');
		$m->set('filterFormName', 'filter_images');
		$view->setModel($m, true);

		$mode = $this->input->get('mode', null, 'word');
		if (!$mode) $mode = $this->input->cookie->get('meedya_eig', 'G');
		$this->input->cookie->set('meedya_eig', $mode);
		$view->mode = $mode;

		$view->display();
	}


	// display a screen/form for editing gallery configuration
	public function doConfig ()
	{
		if ($this->nope()) return;
		$view = $this->getView('manage','html');
		$view->itemId = $this->mnuItm;
		$view->setLayout('config');
		$m = $this->getModel('meedya');
		$view->html5slideshowCfg = $m->getCfg('ss');
		$view->setModel($this->getModel('manage'), true);
		$view->isAdmin = true;
		$view->album = null;
		$view->display();
	}


	// present a selected set of items to edit
	public function imgsEdit ()
	{
		if ($this->nope()) return;
		$view = $this->getView('manage','html');
		$view->setLayout('imgedit');
		$m = $this->getModel('manage');
		$itms = $this->input->post->get('slctimg',[],'array');
		$view->iids = $m->getImages($itms);
		$view->referer = $this->input->server->getRaw('HTTP_REFERER');
		$view->display();
	}


	public function imgEdit ()
	{
		if ($this->nope()) return;
		$view = $this->getView('manage','html');
		$view->setLayout('imgedit');
		$m = $this->getModel('manage');
		$itms = explode('|',$this->input->get('items','','string'));
		if (!$itms[0]) $itms = $this->input->get('after','','string');
		$view->iids = $m->getImages($itms);
		$view->referer = $this->input->server->getRaw('HTTP_REFERER');
		$view->display();
	}


	public function iedSave ()
	{
		if (!$this->tokenCheck()) return;

		$m = $this->getModel('manage');
		if ($this->input->post->get('save',0,'int')) {
			$attrs = $this->input->post->get('attr',[],'array');
			foreach ($attrs as $k=>$v) {
				$m->updImage($k, $v);
			}
			$this->_nqMsg('Image properties sucessfully saved', 'success');
		}
		$this->setRedirect(base64_decode($this->input->post->get('referer','','base64')));
	}


	public function delAlbum ()
	{
		if (!$this->tokenCheck()) return;

		$aid = $this->input->get('aid', 0, 'int');
		$w = $this->input->get('wipe', false, 'boolean');
		if ($aid) {
			$albs = [$aid];
			$m = $this->getModel('manage');
			$m->removeAlbums($albs, $w);
			$this->_nqMsg('The album was successfully deleted', 'success');
		}
		$this->setRedirect(Route::_('index.php?option=com_meedya&view=manage&limitstart=0&Itemid='.$this->mnuItm, false));
	}


	public function addItemsToAlbum ()
	{
		if (!$this->tokenCheck()) return;

		$this->setRedirect($_SERVER['HTTP_REFERER']);

		$itms = $this->input->post->get('slctimg',[],'array');
		if (!$itms) return;

		$m = $this->getModel('manage');

		$aid = $this->input->post->get('albumid',0,'int');
		if ($aid == 0) return;
		if ($aid < 0) {
			$albttl = $this->input->post->get('nualbnam','New Album','string');
			$albpar = $this->input->post->get('nualbpar',0,'int');
			$albdesc = $this->input->post->get('nualbdesc','','string');
			$aid = $m->addAlbum($albttl, $albpar, $albdesc);
		}
		$m->addItems2Album($itms, $aid);
		$this->_nqMsg('Items added to album', 'success');
	}


	public function imgsAddAlbum ()
	{
		$this->setRedirect($_SERVER['HTTP_REFERER']);
		if (!$this->tokenCheck()) return;

		$itms = $this->input->post->get('slctimg',[],'array');
		if (!$itms) return;

		$m = $this->getModel('manage');
		$aid = $m->addAlbum('New Album');
		$m->addItems2Album($itms, $aid);

		$this->setRedirect(Route::_('index.php?option=com_meedya&view=manage&layout=albedit&aid='.$aid.'&Itemid='.$this->mnuItm, false));
	}


	public function deleteItems ()
	{
		$this->setRedirect($_SERVER['HTTP_REFERER']);
		if (!$this->tokenCheck()) return;

		$itms = $this->input->post->get('slctimg',[],'array');
		$m = $this->getModel('manage');
		$m->deleteItems($itms);
	}


	public function saveConfig ()
	{
		$unchk = ['aA'=>0,'aT'=>0,'uA'=>0,'nW'=>0,'sI'=>0,'aP'=>0,'lS'=>0,'vT'=>0,'vD'=>0];

		$vals = array_merge($unchk, $this->input->post->get('ss',null,'array'));

	//	echo'<xmp>';var_dump($vals);echo'</xmp>';jexit();
		if ($this->input->post->get('save',0,'int')) {
			if (!Session::checkToken()) {
				echo Text::_('JINVALID_TOKEN');
				return;
			}
			$m = $this->getModel('manage');
		//	$m->updateConfig('ss', $vals);
			$this->_nqMsg('Gallery settings sucessfully saved', 'success');
		}
		$this->setRedirect(base64_decode($this->input->post->get('return','','base64')));
	}


	public function importMeedya ()
	{
		$bpath = realpath(MeedyaHelper::userDataPath($this->mnuItm)).'/import/';
		$this->importDir($bpath, 0, $this->getModel('manage'));
	}


	private function importDir ($base, $paid, $mdl)
	{
		static $pp = 1;

		if ($h = opendir($base)) {
			while (false !== ($entry = readdir($h))) {
				if ($entry[0] != '.' && $entry != 'index.html') {
					if (is_dir($base.$entry)) {
						// make album
						echo "[{$entry}]<br />";
						$nua = $mdl->addAlbum($entry, $paid);
						// process dir
						$this->importDir($base.$entry.'/', $nua, $mdl);
					} else {
						// add item
						echo "{$paid}::{$entry}<br />";
						$mdl->storeFile(['name'=>$entry, 'title'=>pathinfo($entry, PATHINFO_FILENAME)], $paid, $base);
					}
				}
			}
			closedir($h);
		}
	}


	// save changes made to an album
	public function saveAlbum ()
	{
		$this->setRedirect($_SERVER['HTTP_REFERER']);
		if (!$this->tokenCheck()) return;

		$aid = $this->input->post->get('aid',0,'int');
		$flds = [];
		$flds['thumb'] = $this->input->post->get('albthmid',0,'int');
		$flds['title'] = $this->input->post->get('albttl','','string');
		$flds['desc'] = $this->input->post->get('albdsc','','raw');
		$thmo = $this->input->post->get('thmord','_','string');
		// don't save thumb order unless it is something valid
		if ($thmo != '_') {
			$flds['items'] = $thmo;
		}
		$flds['visib'] = $this->input->post->get('pubalb',0,'int');

		$m = $this->getModel('manage');
		$m->saveAlbum($aid, $flds);

		$this->_nqMsg('Album properties sucessfully saved', 'success');
		$this->setRedirect(base64_decode($this->input->post->get('referer','','base64')));
	}


	private function tokenCheck ()
	{
		if (!Session::checkToken()) {
			$this->_nqMsg(Text::_('JINVALID_TOKEN'), 'error');
			return false;
		}
		return true;

		if (!Session::checkToken()) {
			header('HTTP/1.1 403 Not Allowed');
			jexit(Text::_('JINVALID_TOKEN'));
		}
	}

	private function nope ()
	{
		$uid = Factory::getUser()->get('id');
		if ($uid) return false;
		$this->_nqMsg(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
	//	$this->setRedirect('index.php');
		return true;
	}

	private function _nqMsg ($msg, $tone='info')
	{
		$this->app->enqueueMessage($msg, $tone);
	}


	private function _log ($msg)
	{
		if (RJC_DBUG) file_put_contents('ILOG.txt', $msg, FILE_APPEND);
	}

}
