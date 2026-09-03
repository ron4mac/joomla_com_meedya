<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2023-2026 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.6.0
*/
namespace RJCreations\Component\Meedya\Site\View\Album;

use RJCreations\Component\Meedya\Site\Model\AlbumModel;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use RJCreations\Library\RJUserCom;
use RJCreations\Component\Meedya\Site\View\MeedyaView;
use RJCreations\Component\Meedya\Site\Helper\MeedyaHelper;

//require_once JPATH_BASE . '/components/com_meedya/src/View/MeedyaView.php';

class HtmlView extends MeedyaView
{
	public $title;
	public $desc;
	public $albums;
	public $isSearch;
	public $six;
	public $pathWay;
	public $useFanCB;
	protected $aid;
	protected $fulv = true;

	public function __construct ($config = [])
	{
		if (RJC_DBUG) MeedyaHelper::log('MeedyaViewAlbum');
		$app = Factory::getApplication();
		$key = base64_decode($app->getInput()->get->get('key', '', 'base64'));
		if ($key) {
			$data = MeedyaHelper::decodeKey($key);
			$prms = json_decode($data);
			$config['inst'] = $prms;
			$this->fulv = false;
			if (RJC_DBUG) MeedyaHelper::log(print_r($config,true));
		}
		parent::__construct($config);
	}

	public function display ($tpl = null): void
	{
		$m = $this->getModel();
		$this->state = $m->getState();	//echo'<xmp>';var_dump($this->state);echo'</xmp>';	//echo get_class($this->state);
		$this->aid = $this->state->get('album.id');
		$this->items = $m->getItems();
		$this->title = $m->getTitle();
		$this->desc = $m->getDesc();
		$this->albums = $this->fulv ? $m->getAlbums() : [];

		$this->isSearch = false;
		$this->six = 0;

		// build the bread crumbs
		$pw = $this->app->getPathWay();
//		$pw->setItemName(0, '<i class="icon-home-2" title="Gallery Home"></i>');
		$apw = $m->getAlbumPath($this->aid);
		foreach ($apw as $ap) {
			foreach ($ap as $k => $v) {
				if ($k != $this->aid) {
					$pw->addItem($v, Route::_('index.php?option=com_meedya&view=album&aid='.$k.'&Itemid='.$this->itemId, false));
				}
			}
		}
		$this->pathWay = $pw->getPathway();

		// probably unnecessary pagination 
		$this->pagination = $m->getPagination();

//		$this->app->setHeader('Access-Control-Allow-Origin','http://picframe.local/',true);
//		$this->app->setHeader('Referrer-Policy','unsafe-url',true);

		if ($this->items || $this->albums) {
			$this->useFanCB = true;
			parent::display($tpl);
		} else {
			parent::display('empty');
		}
	}

	public function picframekey ()
	{
		$parms = [];
		$parms['aid'] = $this->aid;
		$parms['obj'] = RJUserCom::getInstObject();

		$jparms = json_encode($parms);
		$key = Uri::root().'?option=com_meedya&format=raw&task=DispRaw.picframe&key='.urlencode(MeedyaHelper::encodeKey($jparms));
		return base64_encode($key);
	}

	public function sharekey ()
	{
		$parms = [];
		$parms['aid'] = $this->aid;
		$parms['obj'] = RJUserCom::getInstObject();

		$jparms = json_encode($parms);
		$rout = Route::_('?option=com_meedya&view=album&key='.urlencode(MeedyaHelper::encodeKey($jparms)), false);
		return Uri::root() . ltrim($rout, '/');
	}

}
