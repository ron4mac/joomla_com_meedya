<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2026 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.6.0
*/
namespace RJCreations\Component\Meedya\Site\View\Meedya;

use RJCreations\Component\Meedya\Site\Model\MeedyaModel;

defined('_JEXEC') or die;

//use Joomla\CMS\Factory;
//use Joomla\CMS\Router\Route;
//use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use RJCreations\Component\Meedya\Site\View\MeedyaView;

//require_once JPATH_BASE . '/components/com_meedya/src/View/MeedyaView.php';

class HtmlView extends MeedyaView
{
	protected $manage = 1;
//	protected $userPerms = null;

	public function display ($tpl=null): void
	{
		$m = $this->getModel();
//		$this->manage = Factory::getUser()->authorise('core.edit', 'com_meedya');
//		$this->user = Factory::getUser();
//		$this->userPerms = MeedyaHelper::getUserPermissions();

//		echo'<xmp>';var_dump($this->get('State'), $this->itemId);echo'</xmp>';

		$this->state = $m->getState();
		$this->items = $m->getItems();
		foreach ($this->items as &$alb) {
			$alb->isClone = false;
			if ($alb->items && str_starts_with($alb->items, '*')) {
				$alb->isClone = true;
				$alb->oaid = (int) substr($alb->items,1);
			} else {
			//	$alb->items = $alb->items ? count(explode('|',$alb->items)) : 'no';
			}
		}

//		$pathway = $this->app->getPathway();
//		$pathway->addItem('My Added Breadcrumb Link', Route::_(''));
		parent::display($tpl);
	}

}
