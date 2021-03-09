<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2021 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

include_once JPATH_COMPONENT.'/views/meedyaview.php';

jimport('joomla.filesystem.folder');

class MeedyaViewManage extends MeedyaView
{
	public $aid = 0;
	protected $_defaultModel = 'manage';
	protected $manage = 1;

	public function __construct ($config = array())
	{
	//	if (RJC_DBUG) MeedyaHelper::log('MeedyaViewManage');
		parent::__construct($config);
	}

	public function display ($tpl=null)
	{
		$this->state = $this->get('State');	//var_dump($this->state);
//		$this->user = Factory::getUser();
//		$this->items = $this->get('Items');

		$this->mparams = !empty($this->itemId) ? Factory::getApplication()->getMenu()->getItem($this->itemId)->getParams() : new Registry();
		//echo'<xmp>';var_dump($this->mparams);echo'</xmp>';

	//	if (RJC_DBUG) MeedyaHelper::log('ViewManage state', $this->state);

		if ($this->state && $this->state->get('album.id')/* ?: 0*/) {
			$this->aid = $this->state->get('album.id');
			$this->album = $this->get('Album');
//			$this->aid = $this->state->get('album.id');
	//		$this->items = $this->get('AlbumItems');
			$this->setLayout('albedit');
		}

	//	if (RJC_DBUG) MeedyaHelper::log('layout='.$this->getLayout());

		Text::script('JACTION_DELETE');
		Text::script('JCANCEL');
		Text::script('COM_MEEDYA_SELECT_SOME');

		switch ($this->getLayout()) {

			case 'newalb':
				$this->albums = $this->get('AlbumsList');
				break;

			case 'images':
				$this->albums = $this->get('AlbumsList');
				$this->action = 'Edit Images';
				$this->iids = $this->get('Items');
				$this->total = count($this->iids);
		//		$this->items = $this->get('Items');
				$this->filterForm = $this->get('FilterForm');	//var_dump('FilterForm',$this->filterForm);jexit();
//				$this->filterForm->setFieldAttribute('limit', 'default', 50, 'list');
				$albs = json_encode($this->get('AllAlbums'));
				$r = $this->filterForm->setFieldAttribute('album', 'albums', $albs, 'filter');	//echo'<xmp>';var_dump('FilterForm',$r,$this->filterForm);jexit();
				$this->activeFilters = $this->get('ActiveFilters');

				$this->pagination = $this->get('Pagination');

				$this->linkUrl = 'index.php?option=com_meedya&task=manage.editImgs';
				break;

			case 'albedit':
				$this->action = 'Edit Album';
				//echo'<xmp>';var_dump($this->state);echo'</xmp>';
				//echo'<xmp>';var_dump($this->album);echo'</xmp>';
			//	$this->pagination = $this->get('Pagination');
				$this->items = explode('|', $this->album['items']);
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
				$this->galStruct = MeedyaHelper::getGalStruct($this->get('AllAlbums'));
				break;

			case 'upload':
				$this->action = 'Upload Images';
			//echo'<pre>';var_dump(JComponentHelper::getParams('com_meedya'));
			//var_dump($this->params);
	//			$this->totStore = (int)$this->get('StorageTotal');
				$user = Factory::getUser();
				$uid = $user->get('id');
				$this->params = Factory::getApplication()->getParams();		//echo'<pre>';var_dump($this->params);echo'</pre>';
				$this->galid = base64_encode($this->params->get('instance_type').':'.$this->params->get('owner_group').':'.$uid);
			//	$this->state = $this->get('State');
//				$this->curalb = 0;
// @+@+@+@+@+@+@+@+@ get media types from config
				$this->uplodr = $this->params->get('upload_ap','UL');
				$this->acptmime = 'accept="image/*,video/*" ';
			//	$this->albums = $this->get('AlbumsList');
				$this->maxUploadFS = MeedyaHelper::maxUpload($this->mparams->get('maxUpload'));
				$this->maxupld = MeedyaHelper::formatBytes($this->maxUploadFS);
			//	$this->dbTime = $this->get('DbTime');
				$this->items = array();		// keep parent view from loading items
				break;

			default:
				$this->action = 'Edit Albums';
				$this->albums = $this->get('AlbumsList');
				$this->totStore = (int)$this->get('StorageTotal');
				$this->galStruct = MeedyaHelper::getGalStruct($this->get('AllAlbums'));
				break;

		}

		parent::display($tpl);
	}

}
