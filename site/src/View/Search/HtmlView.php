<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2026 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.6.0
*/
namespace RJCreations\Component\Meedya\Site\View\Search;

use RJCreations\Component\Meedya\Site\Model\SearchModel;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use RJCreations\Component\Meedya\Site\View\MeedyaView;

//require_once JPATH_BASE . '/components/com_meedya/src/View/MeedyaView.php';

class HtmlView extends MeedyaView
{
	public $isSearch;
	public $desc;
	public $albums;
	public $six;
	public $title;
	public $pathWay;
	public $useFanCB;
//	protected $user;
//	protected $params;
	protected $userPerms;
	public $aid;
	public $sterm;

	public function display ($tpl=null): void
	{
		$m = $this->getModel();
//		$this->user = Factory::getUser();
//		$this->params = Factory::getApplication()->getParams();
//		$this->userPerms = MeedyaHelper::getUserPermissions($this->user, $this->params);
		$this->state = $m->getState();	//echo'<xmp>';var_dump($this->state);echo'</xmp>';	//echo get_class($this->state);

		$this->isSearch = true;
		$this->desc = '';
		$this->albums = [];

		$this->six = 0;
		$this->title = 'Search Results';

		// build the bread crumbs
		$pw = $this->app->getPathWay();
//		$pw->setItemName(0, '<i class="icon-home-2" title="Gallery Home"></i>');
		$apw = $m->getAlbumPath($this->aid);
		foreach ($apw as $ap) foreach ($ap as $k => $v) {
			$pw->addItem($v, Route::_('index.php?option=com_meedya&view=album&aid='.$k.'&Itemid='.$this->itemId, false));
		}
		$this->pathWay = $pw->getPathway();


		$this->items = $m->search($this->sterm, $this->aid);
		$this->useFanCB = true;

		parent::display($this->items ? $tpl : 'empty');
	}

}
