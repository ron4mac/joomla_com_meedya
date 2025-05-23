<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2023-2024 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.4.0
*/
namespace RJCreations\Component\Meedya\Site\View\Picframe;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;

class MeedyaViewPicframe extends MeedyaView
{
	protected $aid;

	public function __construct ($config = [])
	{
		if (RJC_DBUG) { MeedyaHelper::log('MeedyaViewAlbum'); }
		parent::__construct($config);
	}

	function display ($tpl = null)
	{
		$this->state = $this->get('State');	//echo'<xmp>';var_dump($this->state);echo'</xmp>';	//echo get_class($this->state);
		$this->aid = $this->state->get('album.id');
		$this->items = $this->get('Items');
		$this->title = $this->get('Title');
		$this->desc = $this->get('Desc');
		$this->albums = $this->get('Albums');
		$m = $this->getModel();

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
		$this->pagination = $this->get('Pagination');

		if ($this->items || $this->albums) {
			$this->useFanCB = true;
			parent::display($tpl);
		} else {
			parent::display('empty');
		}
	}

}
