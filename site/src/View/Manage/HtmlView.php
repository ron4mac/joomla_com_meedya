<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2023-2026 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.6.0
*/
namespace RJCreations\Component\Meedya\Site\View\Manage;

defined('_JEXEC') or die;

//use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\Filesystem\Helper as FilesystemHelper;
use RJCreations\Component\Meedya\Site\View\MeedyaView;
use RJCreations\Component\Meedya\Site\Helper\MeedyaHelper;

class HtmlView extends MeedyaView
{
	public $mparams;
	public $album;
	public $albums;
	public $action;
	public $iids;
	public $total;
	public $filterForm;
	public $activeFilters;
	public $linkUrl;
	public $aThum;
	public $html5slideshowCfg;
	public $galStruct;
	public $galid;
	public $uplodr;
	public $acptmime;
	public $mimatch;
	public $maxUploadFS;
	public $maxupld;
	public $phpupld;
	public $totStore;
	public $aid = 0;
	protected $_defaultModel = 'manage';
	protected $manage = 1;

	public function display ($tpl=null): void
	{
		if (!$this->userPerms->canAdmin) {
			$this->app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
			return;
		}

		$m = $this->getModel();

		$this->state = $m->getState();	//var_dump($this->state);
//		$this->user = Factory::getUser();
//		$this->items = $this->get('Items');

		$this->mparams = !empty($this->itemId) ? $this->app->getMenu()->getItem($this->itemId)->getParams() : new Registry();
		//echo'<xmp>';var_dump($this->mparams);echo'</xmp>';

	//	if (RJC_DBUG) MeedyaHelper::log('ViewManage state', $this->state);

		if ($this->state && $this->state->get('album.id')/* ?: 0*/) {
			$this->aid = $this->state->get('album.id');
			$this->album = $m->getAlbum();
//			$this->aid = $this->state->get('album.id');
	//		$this->items = $this->get('AlbumItems');
			$this->setLayout('albedit');
		}

	//	if (RJC_DBUG) MeedyaHelper::log('layout='.$this->getLayout());

		Text::script('JACTION_DELETE');
		Text::script('JCANCEL');
		Text::script('JYES');
		Text::script('COM_MEEDYA_SELECT_SOME');

		switch ($this->getLayout()) {

			case 'newalb':
				$this->albums = $m->getAlbumsList();
				break;

			case 'images':
				$this->albums = $m->getAlbumsList();
				$this->action = 'Edit Images';
				$this->iids = $m->getItems();
				$this->total = count($this->iids);
		//		$this->items = $this->get('Items');
				$this->filterForm = $m->getFilterForm();	//echo'<xmp>';var_dump('FilterForm',$this->filterForm);echo'</xmp>';jexit();
//				$this->filterForm->setFieldAttribute('limit', 'default', 50, 'list');
				$albs = json_encode($m->getAllAlbums());
				$r = $this->filterForm->setFieldAttribute('album', 'albums', $albs, 'filter');	//echo'<xmp>';var_dump('FilterForm',$r,$this->filterForm);jexit();
				$this->activeFilters = $m->getActiveFilters();

				$this->pagination = $m->getPagination();

				$this->linkUrl = 'index.php?option=com_meedya&task=manage.editImgs&Itemid='.$this->itemId;
				break;

			case 'albedit':
				$this->action = 'Edit Album';
				//echo'<xmp>';var_dump($this->state);echo'</xmp>';
				//echo'<xmp>';var_dump($this->album);echo'</xmp>';
			//	$this->pagination = $this->get('Pagination');
				$this->items = explode('|', $this->album['items']?:'');
				$this->aThum = $this->album['thumb'] ? $this->getAlbumThumb((object)$this->album) : 'media/com_meedya/img/img.png';
				break;

			case 'imgedit':
			//	$this->iids = $this->getModel('manage')->get('Items');
			//	var_dump($this->iids);
				break;

			case 'config':
				$this->action = 'Configure Gallery';
				$this->items = [];		// keep parent view from loading items
				if (!$this->html5slideshowCfg) {
					$this->html5slideshowCfg = MeedyaHelper::$ssDefault;
				}
				$this->galStruct = MeedyaHelper::getGalStruct($m->getAllAlbums());
				break;

			case 'upload':
				$this->action = 'Upload Images';
			//echo'<pre>';var_dump(JComponentHelper::getParams('com_meedya'));
			//var_dump($this->params);
	//			$this->totStore = (int)$this->get('StorageTotal');
//				$user = Factory::getUser();
//				$uid = $user->get('id');
				$this->params = $this->app->getParams();		//echo'<pre>';var_dump($this->params);echo'</pre>';
				$this->galid = base64_encode($this->params->get('instance_type').':'.$this->params->get('owner_group').':'.$this->uid);
			//	$this->state = $this->get('State');
//				$this->curalb = 0;
// @+@+@+@+@+@+@+@+@ get media types from config
				$this->uplodr = $this->params->get('upload_ap','UL');
				$this->acptmime = $this->params->get('videok', 0) ? 'accept="image/*,video/*" ' : 'accept="image/*" ';
				$this->mimatch = $this->params->get('videok', 0) ? 'image\/|video\/' : 'image\/';
			//	$this->albums = $this->get('AlbumsList');
				$this->maxUploadFS = MeedyaHelper::maxUpload($this->mparams->get('maxUpload'));
				$this->maxupld = MeedyaHelper::formatBytes($this->maxUploadFS);
				$this->phpupld = MeedyaHelper::formatBytes(FilesystemHelper::getFileUploadMaxSize(false));
			//	$this->dbTime = $this->get('DbTime');
				$this->items = [];		// keep parent view from loading items
				break;

			default:
				$this->action = 'Edit Albums';
				$this->albums = $m->getAlbumsList();
				$this->totStore = (int)$m->getStorageTotal();
				$this->galStruct = MeedyaHelper::getGalStruct($m->getAllAlbums());
				break;

		}

		parent::display($tpl);
	}

}
