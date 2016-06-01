<?php
// no direct access
defined('_JEXEC') or die;

//require_once JPATH_COMPONENT.'/helpers/meedya.php';

no class MeedyaControllerAManage extends JControllerLegacy
{
/*
	public function upload ()
	{
		$this->input->set('view', 'upload');
	}

	public function upfile ()
	{
		$galid = base64_decode($this->input->get('galid', '', 'base64'));
		$file = $this->input->files->get('userpicture');

		$m = $this->getModel('manage');
		$m->storeFile($file, $this->input->get('album', 0, 'int'));

//		jexit();
	}

	public function newAlbum ()
	{
		$m = $this->getModel('manage');
		$a = $this->input->get('albnam','A NEW ALBUM','string');
		$aid = $m->addAlbum($a);
		header("Content-type: text/xml; charset=utf-8");
		echo "<?xml version='1.0' encoding='UTF-8'?>";
		echo "<note>";
		echo "<name>{$a}</name>";
		echo "<id>{$aid}</id>";
		if (!$aid) {
			echo "<error>Could not create album: {$a}</error>";
		}
		echo "</note>";
//		jexit();
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
*/
	public function removeItems ()
	{
		$aid = $this->input->post->get('aid','','int');
		$parm = $this->input->post->get('items','','string');
		$items = explode('|',$parm);
		$m = $this->getModel('amanage');
		$m->removeItems($aid, $items);
	}

	public function delItems ()
	{
	}
}
