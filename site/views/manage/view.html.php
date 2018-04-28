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
	protected $manage = 1;

	public function __construct ($config = array())
	{
		if (RJC_DBUG) { MeedyaHelper::log('MeedyaViewManage'); }
		parent::__construct($config);
	}

	public function display ($tpl=null)
	{
		$this->state = $this->get('State');
//		$this->user = JFactory::getUser();
//		$this->items = $this->get('Items');

//echo'<xmp>';var_dump($this->state);echo'</xmp>';
		if ($this->state && $this->state->get('album.id') ?: 0) {
			$this->album = $this->get('Album');
			$this->aid = $this->state->get('album.id');
			$this->items = $this->get('AlbumItems');
			$this->setLayout('albedit');
		}

		if (RJC_DBUG) { MeedyaHelper::log('layout='.$this->getLayout()); }

		switch ($this->getLayout()) {

			case 'newalb':
				$this->albums = $this->getModel()->getAlbumsList();
				break;

			case 'images':
				$this->action = 'Edit Images';
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
				$this->aThum = $this->album['thumb'] ? $this->getAlbumThumb((object)$this->album) : 'components/com_meedya/static/img/img.png';
				break;
			case 'imgedit':
			//	$this->iids = $this->getModel('manage')->get('Items');
			//	var_dump($this->iids);
				break;

			case 'config':
				$this->action = 'Configure Gallery';
				$this->items = array();		// keep parent view from loading items
				if (!$this->html5slideshowCfg) {
					$this->html5slideshowCfg = MeedyaHelper::$ssDefault;
				}
				$this->galStruct = MeedyaHelper::getGalStruct($this->getModel()->getAllAlbums());
				break;

			case 'upload':
				$this->action = 'Upload Images';
			//echo'<pre>';var_dump(JComponentHelper::getParams('com_meedya'));
			//var_dump($this->params);
	//			$this->totStore = (int)$this->get('StorageTotal');
				$user = JFactory::getUser();
				$uid = $user->get('id');
				$this->params = JFactory::getApplication()->getParams();		//echo'<pre>';var_dump($this->params);echo'</pre>';
				$this->galid = base64_encode($this->params->get('instance_type').':'.$this->params->get('owner_group').':'.$uid);
			//	$this->state = $this->get('State');
				$this->curalb = 0;
// @+@+@+@+@+@+@+@+@ get media types from config
				$this->acptmime = 'accept="image/*,video/*" ';
			//	$this->albums = $this->get('AlbumsList');
				$this->maxUploadFS = MeedyaHelper::maxUpload($this->params->get('maxUpload'));
				$this->maxupld = MeedyaHelper::formatBytes($this->maxUploadFS);
			//	$this->dbTime = $this->get('DbTime');
				$this->items = array();		// keep parent view from loading items
				break;

			default:
				$this->action = 'Edit Albums';
				$this->albums = $this->getModel()->getAlbumsList();
				$this->totStore = (int)$this->get('StorageTotal');
				$this->galStruct = MeedyaHelper::getGalStruct($this->getModel()->getAllAlbums());
				break;

		}

		parent::display($tpl);
	}

}