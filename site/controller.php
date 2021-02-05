<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2020 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;

class MeedyaController extends JControllerLegacy
{
	protected $uid = 0;
	protected $mnuItm;

	public function __construct ($config = [])
	{
		if (RJC_DBUG) { MeedyaHelper::log('MeedyaController'); }
		parent::__construct($config);
		if (JDEBUG) { JLog::addLogger(['text_file'=>'com_meedya.log.php'], JLog::ALL, ['com_meedya']); }
		$this->uid = Factory::getUser()->get('id');
		$this->mnuItm = $this->input->getInt('Itemid', 0);
		if ($this->mnuItm) {
			Factory::getApplication()->setUserState('com_meedya.instance', $this->mnuItm.':'.MeedyaHelper::getInstanceID().':'.$this->uid);
		}
	}

	public function display ($cachable = false, $urlparams = false)
	{
	//	if (!$this->uid) {
	//		Factory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
	//		return;
	//	}
		if (!file_exists(MeedyaHelper::userDataPath())) {
			//set to a view that has no model
			$this->input->set('view', 'startup');
		} else {
			$view = $this->getView('meedya','html');
			$view->itemId = $this->mnuItm;
		}
		return parent::display($cachable, $urlparams);
	}

	public function begin ()
	{
		if (!$this->uid) return;
		$htm = '<!DOCTYPE html><title></title>';
		$udp = MeedyaHelper::userDataPath();
		mkdir($udp.'/img', 0777, true);
		mkdir($udp.'/thm', 0777, true);
		mkdir($udp.'/med', 0777, true);
		file_put_contents($udp.'/index.html', $htm);
		file_put_contents($udp.'/img/index.html', $htm);
		file_put_contents($udp.'/thm/index.html', $htm);
		file_put_contents($udp.'/med/index.html', $htm);
		$this->setRedirect(Route::_('index.php?option=com_meedya&Itemid='.$this->mnuItm, false));
	}

	public function search ()
	{
		$m = $this->getModel('album');	//, '', array('ignore_request' => false));
		$m->setState('sterm', $this->input->post->getString('sterm', '*'));
		$view = $this->getView('album','html');
		$view->setModel($m, true);
		$view->isSearch = true;
		$view->display();
	}

}
