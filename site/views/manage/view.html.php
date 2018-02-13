<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2017 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

include_once JPATH_COMPONENT.'/views/meedyaview.php';

class MeedyaViewManage extends MeedyaView
{
	protected $_defaultModel = 'manage';

	public function __construct ($config = array())
	{
		if (JDEBUG) {
			JLog::add('MeedyaViewManage', JLog::DEBUG, 'com_meedya');
		}
		parent::__construct($config);
	}

	public function display ($tpl=null)
	{
		$this->state = $this->get('State');
//		$this->items = $this->get('Items');

//echo'<xmp>';var_dump($this->state);echo'</xmp>';
		if ($this->state && $this->state->get('album.id') ?: 0) {
			$this->album = $this->get('Album');
			$this->aid = $this->state->get('album.id');
			$this->items = $this->get('AlbumItems');
			$this->setLayout('albedit');
		}

		if (JDEBUG) {JLog::add('layout='.$this->getLayout(), JLog::DEBUG, 'com_meedya');}

		switch ($this->getLayout()) {

			case 'newalb':
				$this->albums = $this->getModel()->getAlbumsList();
				break;

			case 'images':
				$this->iids = $this->get('Items');
				$this->total = count($this->iids);
		//		$this->items = $this->get('Items');
		//		$this->getModel()->set('filterFormName', 'filter_images');
				$this->filterForm = $this->get('FilterForm');	//var_dump('FilterForm',$this->filterForm);jexit();
				$albs = json_encode($this->getModel()->getAllAlbums());
				$r = $this->filterForm->setFieldAttribute('album', 'albums', $albs, 'filter');	//echo'<xmp>';var_dump('FilterForm',$r,$this->filterForm);jexit();
		//		$this->filterForm = $this->getModel()->loadForm($this->context . '.filter', 'filter_images', array('control' => '', 'load_data' => true, '_DB' => 'XXYYZZ'));
				$this->activeFilters = $this->get('ActiveFilters');

				$this->pagination = $this->get('Pagination');

				$this->linkUrl = 'index.php?option=com_meedya&task=manage.editImgs';
				break;
			case 'albedit':
				//echo'<xmp>';var_dump($this->state);echo'</xmp>';
				//echo'<xmp>';var_dump($this->album);echo'</xmp>';
				$this->aThum = $this->getAlbumThumb((object)$this->album);
				break;
			case 'imgedit':
			//	$this->iids = $this->getModel('manage')->get('Items');
			//	var_dump($this->iids);
				break;

			case 'config':
				if (!$this->html5slideshowCfg) {
					$this->html5slideshowCfg = MeedyaHelper::$ssDefault;
				}
				$this->items = array();		// keep parent view from loading items
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
				$this->items = array();		// keep parent view from loading items
				break;

			default:
				$this->albums = $this->getModel()->getAlbumsList();
				$this->totStore = (int)$this->get('StorageTotal');
				break;

		}

		parent::display($tpl);
	}

}