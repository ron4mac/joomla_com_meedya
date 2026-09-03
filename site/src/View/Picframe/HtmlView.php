<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2023-2026 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.6.0
*/
namespace RJCreations\Component\Meedya\Site\View\Picframe;

use RJCreations\Component\Meedya\Site\Model\PicframeModel;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use RJCreations\Component\Meedya\Site\View\MeedyaView;
use RJCreations\Component\Meedya\Site\Helper\MeedyaHelper;

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

	public function __construct ($config = [])
	{
		if (RJC_DBUG) { MeedyaHelper::log('MeedyaViewAlbum'); }
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
		$this->albums = $m->getAlbums();

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

		if ($this->items || $this->albums) {
			$this->useFanCB = true;
			parent::display($tpl);
		} else {
			parent::display('empty');
		}
	}

}
