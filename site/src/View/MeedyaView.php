<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2023-2026 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.6.0
*/
namespace RJCreations\Component\Meedya\Site\View;

defined('_JEXEC') or die;

/*
 * This is a base view class to (hopefully) avoid duplication of code needed by all views
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use RJCreations\Library\RJUserCom;
use RJCreations\Component\Meedya\Site\Helper\MeedyaHelper;
use RJCreations\Component\Meedya\Site\Helper\HtmlMeedya;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

\JLoader::register('HtmlElementObject', JPATH_SITE . '/components/com_meedya/classes/HtmlObject.php');

HTMLHelper::_('bootstrap.dropdown');
HTMLHelper::_('bootstrap.tooltip', '.hastip', ['placement'=>'bottom']);

class MeedyaView extends BaseHtmlView
{
	// some common properites for all views
	public $itemId;
	protected $app;
	protected $state;
	protected $items;
	protected $user;
	protected $uid;
	protected $params;
	protected $userPerms;
	protected $meedyaID;
	protected $gallpath;
	protected $pagination;
	protected $btmscript = [];	// accumulate here any scripts that will render at the bottom of content

//	protected $instance;
	protected $jDoc;

	public function __construct ($config = [])
	{
		if (RJC_DBUG) MeedyaHelper::log('MeedyaView');
		parent::__construct($config);
		$this->app = Factory::getApplication();
		$this->user = $this->app->getIdentity();
		$this->uid = $this->user->get('id');
		$this->params = $this->app->getParams();
		if (empty($this->itemId)) {
			$this->itemId = $this->app->input->getInt('Itemid', 0);
		}
		$this->userPerms = MeedyaHelper::getUserPermissions($this->user, $this->params);
	//	$this->meedyaID = MeedyaHelper::getInstanceID();
		$this->gallpath = RJUserCom::getStoragePath(empty($config['inst']) ? null : $config['inst']->obj);

//		$this->instance = $this->app->getUserState('com_meedya.instance', '::');		//var_dump([$this->meedyaID,$this->instance]);
		$this->jDoc = Factory::getDocument();
		$pgidparm = property_exists($this, 'pgid') && $this->pgid !== null ? '&pgid='.$this->pgid : '';
		$aurl = Route::_('index.php?option=com_meedya&view='.$pgidparm.'&Itemid='.$this->itemId.'&task=', false);
		$rurl = Route::_('index.php?option=com_meedya&format=raw'.$pgidparm.'&Itemid='.$this->itemId, false);
		$this->jDoc->addScriptOptions('Meedya',['aURL' => $aurl,'rawURL' => $rurl,'isAdmin' => $this->userPerms->canAdmin]);
	}

	public function display ($tpl = null): void
	{
		if (RJC_DBUG) { MeedyaHelper::log('MeedyaView - display'); }
		$this->pagination = $this->getModel()->getPagination();

		// add javascript to fetch images only when scrolled into view
//		MeedyaHelper::addScript('echo');

		parent::display($tpl);
		if ($this->btmscript) echo "<script id=\"btms\" type=\"text/javascript\">\n".implode("\n", $this->btmscript)."\n</script>";
	}

	public function add2btmscript ($scr): void
	{
		$this->btmscript[] = $scr;
	}

	protected function getAlbumThumb ($albrec)
	{
		$pics = ($albrec->items && !$albrec->isClone) ? explode('|', $albrec->items) : [];
		if (!$albrec->thumb) {
			$albrec->thumb = $pics !== [] ? $pics[0] : false;
		}
		if ($albrec->thumb) {
			return $this->gallpath.'/thm/'.$this->getItemThumb($albrec->thumb);
		}
		return 'media/com_meedya/img/noimages.jpg';
	}

	protected function getItemThumb ($iid)
	{
		$m = $this->getModel();
		return $m->getItemThumbFile($iid);
	}

	protected function getItemThumbPlus ($iid)
	{
		$m = $this->getModel();
		return $m->getItemThumbFilePlus($iid);
	}

	protected function getItemFile ($iid)
	{
		$m = $this->getModel();
		return $m->getItemFile($iid);
	}

}
