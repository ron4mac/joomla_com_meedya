<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2017 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

class MeedyaControllerManage extends JControllerLegacy
{
	protected $default_view = 'manage';

	public function __construct ($config = array())
	{
	//	$config['name'] = $this->default_view;
		if (RJC_DBUG) { MeedyaHelper::log('MeedyaControllerManage'); }
		parent::__construct($config);
	}

/*	public function display ($cachable = false, $urlparams = false)
	{
		if (RJC_DBUG) { MeedyaHelper::log('MeedyaControllerManage : display'); }
		$aid = $this->input->get->get('aid',0,'int');
		if ($aid) {
			$view = $this->getView('manage','html');
			$view->setLayout('album_edit');
		}
		return parent::display($cachable, $urlparams);
	}
*/
	public function upload ()
	{
		$this->input->set('view', 'upload');
	}

	public function imgsEdit ()
	{
		$view = $this->getView('manage','html');
		$view->setLayout('imgedit');
		$m = $this->getModel('manage');
		$itms = $this->input->post->get('slctimg',[],'array');
//		if (!$itms[0]) $itms = $this->input->get('after','','string');
		$view->iids = $m->getImages($itms);
		$view->referer = $this->input->server->getRaw('HTTP_REFERER');
		$view->display();
	}

	public function imgEdit ()
	{
		$view = $this->getView('manage','html');
		$view->setLayout('imgedit');
		$m = $this->getModel('manage');
		$itms = explode('|',$this->input->get('items','','string'));
		if (!$itms[0]) $itms = $this->input->get('after','','string');
		$view->iids = $m->getImages($itms);
		$view->referer = $this->input->server->getRaw('HTTP_REFERER');
	//	$view->items = $view->iids;
	//	$view->setModel('manage');
		$view->display();
	}

	public function iedSave ()
	{
		$m = $this->getModel('manage');
	//	echo'<xmp>';var_dump($this->input->post->get('attr',array(),'array'));echo'</xmp>';jexit();
		if ($this->input->post->get('save',0,'int')) {
			$attrs = $this->input->post->get('attr',array(),'array');
			foreach ($attrs as $k=>$v) {
				$m->updImage($k, $v);
			}
			JFactory::getApplication()->enqueueMessage('Image properties sucessfully saved');
		}
		$this->setRedirect(base64_decode($this->input->post->get('referer','','string')));
	}

	public function delAlbum ()
	{
		$aid = $this->input->get('aid', 0, 'int');
		$w = $this->input->get('wipe', false, 'boolean');
		if ($aid) {
			$albs = array($aid);
			$m = $this->getModel('manage');
			$m->removeAlbums($albs, $w);
			JFactory::getApplication()->enqueueMessage('The album would have been successfully deleted');
		}
		$this->setRedirect(JRoute::_('index.php?option=com_meedya&view=manage&limitstart=0', false));
	}

//	public function delAlbums ()
//	{
//		$a = $this->input->get('albs', '', 'string');
//		$w = $this->input->get('wipe', false, 'boolean');
//		if ($a) {
//			$albs = explode('|', $a);
//			$m = $this->getModel('manage');
//			$m->removeAlbums($albs, $w);
//		}
//		$this->setRedirect(JRoute::_('index.php?option=com_meedya&view=manage&limitstart=0', false));
//	}

	public function delItems ()
	{
	}

	public function doUpload ()
	{
		$view = $this->getView('manage','html');
		$m = $this->getModel('manage');
		$view->albums = $m->getAlbumsList();
		$view->dbTime = $m->getDbTime();
		$view->totStore = (int)$m->getStorageTotal();
		$view->setLayout('upload');
		$view->display();
	}

	public function __editImgs ()
	{
		$view = $this->createView('Images', 'MeedyaView', 'html');	//$this->getView('manage','html');
		$view->setLayout('imgedit');
		$m = $this->createModel('Images','MeedyaModel');		//$this->getModel('manage');
		$view->setModel($m, true);
	//	$itms = explode('|',$this->input->post->get('items','','string'));
	//	if (!$itms[0]) $itms = $this->input->get('after','','string');
	//	$view->iids = $m->getItems();
		$view->itemId = $this->input->getInt('Itemid');
		$view->mode = $this->input->get('mode','L','string');
		$view->display();
	}

	public function editImgs ()
	{
		$view = $this->getView('manage','html');
		$view->setLayout('images');
		$m = $this->getModel('manage');
		$m->set('filterFormName', 'filter_images');
		$view->setModel($m, true);
	//	$itms = explode('|',$this->input->post->get('items','','string'));
	//	if (!$itms[0]) $itms = $this->input->get('after','','string');
	//	$view->iids = $m->getItems();
		$view->itemId = $this->input->getInt('Itemid');

		$mode = $this->input->get('mode', null);
		if (!$mode) $mode = $this->input->cookie->get('meedya_eig', 'L');
		$this->input->cookie->set('meedya_eig', $mode);
		$view->mode = $mode;

		$view->display();
	}

	public function doConfig ()
	{
		$view = $this->getView('manage','html');
		$view->setLayout('config');
		$m = $this->getModel('meedya');
		$view->html5slideshowCfg = $m->getCfg('ss');
		$view->setModel($this->getModel('manage'), true);
		$view->isAdmin = true;
		$view->album = null;
		$view->display();
	}

	public function saveConfig ()
	{
		$unchk = array('aA'=>0,'aT'=>0,'uA'=>0,'nW'=>0,'sI'=>0,'aP'=>0,'lS'=>0,'vT'=>0,'vD'=>0);

		$vals = array_merge($unchk, $this->input->post->get('ss',null,'array'));

	//	echo'<xmp>';var_dump($vals);echo'</xmp>';jexit();
		if ($this->input->post->get('save',0,'int')) {
			if (!JSession::checkToken()) {
				echo'bad token';
				return;
			}
			$m = $this->getModel('manage');
		//	$m->updateConfig('ss', $vals);
			JFactory::getApplication()->enqueueMessage('Gallery settings sucessfully saved');
		}
		$this->setRedirect(base64_decode($this->input->post->get('return','','base64')));
	}

	public function importMeedya ()
	{
		$bpath = realpath(MeedyaHelper::userDataPath()).'/import/';
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
						$mdl->storeFile(array('name'=>$entry, 'title'=>pathinfo($entry, PATHINFO_FILENAME)), $paid, $base);
					}
				}
			}
			closedir($h);
		}
	}

}