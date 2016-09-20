<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2016 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

//require_once JPATH_COMPONENT.'/helpers/meedya.php';

class MeedyaControllerManage extends JControllerLegacy
{
	public function __construct ($config = array())
	{
		parent::__construct($config);
		if (JDEBUG) { JLog::addLogger(array('text_file'=>'com_meedya.log.php'), JLog::ALL, array('com_meedya')); }
	}

	public function upload ()
	{
		$this->input->set('view', 'upload');
	}

	public function imgEdit ()
	{
		$view = $this->getView('manage','html');
		$view->setLayout('imgedit');
		$m = $this->getModel('manage');
		$itms = explode('|',$this->input->post->get('items','','string'));
		if (!$itms[0]) $itms = $this->input->get('after','','string');
		$view->iids = $m->getImages($itms);
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
		}
	}

	public function delAlbums ()
	{
		$a = $this->input->get('albs','','string');
		$w = $this->input->get('wipe',false,'boolean');
		if ($a) {
			$albs = explode('|', $a);
			$m = $this->getModel('manage');
			$m->removeAlbums($albs, $w);
		}
		$this->setRedirect(JRoute::_('index.php?option=com_meedya&view=manage&limitstart=0', false));
	}

	public function delItems ()
	{
	}

	public function doUpload ()
	{
		$view = $this->getView('manage','html');
		$m = $this->getModel('manage');
		$view->albums = $m->getAlbumsList();
		$view->dbTime = $m->getDbTime();
		$view->setLayout('upload');
		$view->display();
	}

	public function editImgs ()
	{
		$view = $this->createView('Images', 'MeedyaView', 'html');	//$this->getView('manage','html');
		$view->setLayout('imgedit');
		$m = $this->createModel('Images','MeedyaModel');		//$this->getModel('manage');
		$view->setModel($m, true);
	//	$itms = explode('|',$this->input->post->get('items','','string'));
	//	if (!$itms[0]) $itms = $this->input->get('after','','string');
	//	$view->iids = $m->getItems();
		$view->itemId = $this->input->getInt('Itemid');
		$view->display();
	}

	public function doConfig ()
	{
		$view = $this->getView('manage','html');
		$view->setLayout('config');
		$m = $this->getModel('meedya');
		$view->html5slideshowCfg = $m->getCfg('ss');
		$view->isAdmin = true;
		$view->album = null;
		$view->display();
	}

	public function saveConfig ()
	{
		$app = JFactory::getApplication();
		$input = $app->input->post;
		echo'<xmp>';var_dump($input->post->get('ss',null,'array'));echo'</xmp>';
		if ($input->get('save',0,'int')) {
			if (!JSession::checkToken()) {
				echo'bad token';
				return;
			}
			$m = $this->getModel('manage');
		//	$m->updateConfig('ss',$html5slideshowCfg);
			$app->enqueueMessage('Gallery settings sucessfully saved');
		}
		$this->setRedirect(base64_decode($input->get('return','','base64')));
	}

}