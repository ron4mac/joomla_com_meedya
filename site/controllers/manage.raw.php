<?php
defined('_JEXEC') or die;

JLoader::register('JHtmlMeedya', JPATH_COMPONENT . '/helpers/html/meedya.php');

class MeedyaControllerManage extends JControllerLegacy
{
	public function __construct ($config = array())
	{
		parent::__construct($config);
		if (JDEBUG) { JLog::addLogger(array('text_file'=>'com_meedya.log.php'), JLog::ALL, array('com_meedya')); }
	}

	// task to receive and store uploaded files
	public function upfile ()
	{
		$galid = base64_decode($this->input->get('galid', '', 'base64'));
		$file = $this->input->files->get('userpicture');

		$m = $this->getModel('manage');
		$m->storeFile($file, $this->input->get('album', 0, 'int'));
	}

	// task to create a new album
	public function newAlbum ()
	{
		$a = $this->input->post->get('albnam', 'A NEW ALBUM', 'string');
		$p = $this->input->post->get('paralb', 0, 'int');
		$d = $this->input->post->get('albdesc', null, 'string');
		$m = $this->getModel('manage');
		$aid = $m->addAlbum($a, $p, $d);
		if (!$aid) {
			header("HTTP/1.0 400 Could not create album: {$a}");
		} else {
			$albs = $m->getAlbumsList();
			echo JHtml::_('meedya.albumsHierOptions', $albs, $aid);
		}
	}

	// task to remove items from an album
	public function removeItems ()
	{
		$aid = $this->input->post->get('aid','','int');
		$parm = $this->input->post->get('items','','string');
		$items = explode('|',$parm);
		$m = $this->getModel('manage');
		$m->removeItems($aid, $items);
	}

}