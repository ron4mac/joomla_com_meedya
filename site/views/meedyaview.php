<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

/*
 * This is a base view class to (hopefully) avoid duplication of code needed by all views
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;

// provide all views with a HTMLHelper class
JLoader::register('JHtmlMeedya', JPATH_COMPONENT . '/helpers/html/meedya.php');
// and our htmlobject class
JLoader::register('HtmlElementObject', JPATH_COMPONENT . '/classes/HtmlObject.php');

HTMLHelper::_('bootstrap.dropdown');
HTMLHelper::_('bootstrap.tooltip');

class MeedyaView extends JViewLegacy
{
	// some common properites for all views
	protected $app;
	protected $state;
	protected $items = null;
	protected $user;
	protected $uid;
	protected $params;
	protected $userPerms = null;
	protected $meedyaID;
	protected $gallpath;
	protected $pagination;
	protected $btmscript = [];	// accumulate here any scripts that will render at the bottom of content

	protected $instance;
	protected $jDoc;

	public function __construct ($config = [])
	{
		if (RJC_DBUG) {
			MeedyaHelper::log('MeedyaView');
		}
		parent::__construct($config);
		$this->user = Factory::getUser();
		$this->uid = $this->user->get('id');
		$this->app = Factory::getApplication();
		$this->params = $this->app->getParams();
		if (empty($this->itemId)) {
			$this->itemId = $this->app->input->getInt('Itemid', 0);
		}
		$this->userPerms = MeedyaHelper::getUserPermissions($this->user, $this->params);
		$this->meedyaID = MeedyaHelper::getInstanceID();
		$this->gallpath = MeedyaHelper::userDataPath($this->itemId);

		$this->instance = $this->app->getUserState('com_meedya.instance', '::');
		$this->jDoc = Factory::getDocument();
		$pgidparm = isset($this->pgid) ? '&pgid='.$this->pgid : '';
		$rurl = Route::_('index.php?option=com_meedya&format=raw'.$pgidparm.'&Itemid='.$this->itemId, false);
		$this->jDoc->addScriptOptions('Meedya',['rawURL' => $rurl]);
	}

	public function display ($tpl = null)
	{
		if (RJC_DBUG) { MeedyaHelper::log('MeedyaView - display'); }
		$this->pagination = $this->get('Pagination');

		// add javascript to fetch images only when scrolled into view
		MeedyaHelper::addScript('echo');

		parent::display($tpl);
		if ($this->btmscript) echo "<script type=\"text/javascript\">\n".implode("\n", $this->btmscript)."\n</script>";
	}

	protected function getAlbumThumb ($albrec)
	{
		$pics = $albrec->items ? explode('|', $albrec->items) : [];
		if (!$albrec->thumb) {
			$albrec->thumb = $pics ? $pics[0] : false;
		}
		if ($albrec->thumb) {
			$thum = $this->gallpath.'/thm/'.$this->getItemThumb($albrec->thumb);
		} else {
			$thum = 'components/com_meedya/static/img/noimages.jpg';
		}
		return $thum;
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
