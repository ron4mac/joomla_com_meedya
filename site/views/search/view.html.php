<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2021 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;

include_once JPATH_COMPONENT.'/views/meedyaview.php';

class MeedyaViewSearch extends MeedyaView
{
	protected $user;
	protected $params;
	protected $userPerms = null;
	public $aid;
	public $sterm;

	function display ($tpl=null)
	{
		$this->user = Factory::getUser();
		$this->params = Factory::getApplication()->getParams();
		$this->userPerms = MeedyaHelper::getUserPermissions($this->user, $this->params);
		$this->state = $this->get('State');	//echo'<xmp>';var_dump($this->state);echo'</xmp>';	//echo get_class($this->state);

		$this->isSearch = true;
		$this->desc = '';
		$this->albums = [];

		$this->six = 0;
		$this->title = 'Search Results';

		$m = $this->getModel();

		// build the bread crumbs
		$pw = Factory::getApplication()->getPathWay();
		$pw->setItemName(0, '<i class="icon-home-2" title="Gallery Home"></i>');
		$apw = $m->getAlbumPath($this->aid);
		foreach ($apw as $ap) {
			foreach ($ap as $k => $v) {
				$pw->addItem($v, Route::_('index.php?option=com_meedya&view=album&aid='.$k.'&Itemid='.$this->itemId, false));
			}
		}
		$this->pathWay = $pw->getPathway();


		$this->items = $m->search($this->sterm, $this->aid);

		parent::display($this->items ? $tpl : 'empty');
	}

}
