<?php
defined('_JEXEC') or die;

include_once JPATH_COMPONENT.'/views/meedyaview.php';

class MeedyaViewManage extends MeedyaView
{
	function display ($tpl=null)
	{
		$this->state = $this->get('State');
//		$this->items = $this->get('Items');

//var_dump($this->state);
		if ($this->state && $this->state->get('album.id') ?: 0) {
			$this->album = $this->get('Album');
			$this->aid = $this->state->get('album.id');
			$this->items = $this->get('AlbumItems');
			$this->setLayout('album_edit');
		}

		switch ($this->getLayout()) {

			case 'newalb':
				$this->albums = $this->getModel()->getAlbumsList();
				break;

			case 'imgedit':
			//	$this->iids = $this->getModel('manage')->get('Items');
			//	var_dump($this->iids);
				break;

			case 'config':
				if (!$this->html5slideshowCfg) {
					$this->html5slideshowCfg = MeedyaHelper::$ssDefault;
				}
				break;

			case 'upload':
				$user = JFactory::getUser();
				$uid = $user->get('id');
				$this->params = JFactory::getApplication()->getParams();
				$this->galid = base64_encode($this->params->get('instance_type').':'.$this->params->get('owner_group').':'.$uid);
			//	$this->state = $this->get('State');
				$this->curalb = 0;
				$this->acptmime = 'accept="image/*" ';
			//	$this->albums = $this->get('AlbumsList');
				$this->maxupld = MeedyaHelper::to_KMG($this->params->get('max_upload'));
			//	$this->dbTime = $this->get('DbTime');
				break;

			default:
				$this->albums = $this->getModel()->getAlbumsList();
				$this->totStore = (int)$this->get('StorageTotal');
				break;

		}

		parent::display($tpl);
	}

}