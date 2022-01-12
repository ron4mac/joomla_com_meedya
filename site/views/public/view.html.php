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

class MeedyaViewPublic extends MeedyaView
{
	protected $pgid;

	public function __construct ($config = [])
	{
		if (RJC_DBUG) { MeedyaHelper::log('MeedyaViewPublic'); }
		$this->pgid = Factory::getApplication()->input->get('pgid','','cmd');
		parent::__construct($config);
	}

	function display ($tpl=null)
	{
		$this->state = $this->get('State');

		switch ($this->getLayout()) {
			case 'album':
//				$app = Factory::getApplication();
				$this->pgid = Factory::getApplication()->input->get('pgid','','cmd');
				list($gdir, $gsfx, $aid) = explode('|', base64_decode($this->pgid));
				$this->isSearch = true;
				$this->useFanCB = true;
				$pw = $this->app->getPathWay();
				$pw->setItemName(0, $this->params->get('page_title'));

				$apw = $this->get('AlbumPath');  //$m->getAlbumPath($this->aid);
				foreach ($apw as $ap) {
					foreach ($ap as $k => $v) {
						if ($k != $aid) {
							$pw->addItem($v[0], Route::_('index.php?option=com_meedya&view=public&layout=album&pgid='.$v[1].'&Itemid='.$this->itemId, false));
						}
					}
				}
				//$this->pathWay = [$this->params->get('page_title')];
				$this->pathWay = $pw->getPathway();
				$this->gallpath = $this->get('Gallpath');
				$this->title = $this->get('Title');
				$this->desc = $this->get('Desc');
				$this->albums = $this->get('Albums');
				$this->items = $this->get('AlbumItems');
				$this->params->set('owner', $this->getModel()->getOwnerName($gdir));
				break;
			default:
				$this->items = $this->get('Items');
		}

//		$app = Factory::getApplication();
		$pathway = $this->app->getPathway();
//		$pathway->addItem('My Added Breadcrumb Link', Route::_(''));
		parent::display($tpl);
	}

	protected function getAlbumThumb ($albrec)
	{
		$pics = $albrec->items ? explode('|', $albrec->items) : [];
		if (!$albrec->thumb) {
			$albrec->thumb = $pics ? $pics[0] : false;
		}
		if ($albrec->thumb) {
			$m = $this->getModel();
			if (!isset($albrec->paix)) $albrec->paix = false;
			$gallpath = $m->getGallpath($albrec->paix);
			$thum = $gallpath.'/thm/'.$this->getItemThumbP($albrec->thumb, $albrec->paix);
		} else {
			$thum = 'components/com_meedya/static/img/noimages.jpg';
		}
		return $thum;
	}

	protected function getItemThumbP ($iid, $paix)
	{
		$m = $this->getModel();
		return $m->getItemThumbFile($iid, $paix);
	}

}
