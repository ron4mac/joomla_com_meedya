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

	function display ($tpl=null)
	{
		$this->state = $this->get('State');

		switch ($this->getLayout()) {
			case 'album':
				$pgid = Factory::getApplication()->input->get('pgid','','cmd');
				$this->isSearch = true;
				$this->useFanCB = true;
				$this->pathWay = [];
				$this->gallpath = $this->get('Gallpath');
				$this->title = $this->get('Title');
				$this->desc = $this->get('Desc');
				$this->albums = $this->get('Albums');
				$this->items = $this->get('AlbumItems');
				break;
			default:
				$this->items = $this->get('Items');
		}

		$app = Factory::getApplication();
		$pathway = $app->getPathway();
		$pathway->addItem('My Added Breadcrumb Link', Route::_(''));
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
