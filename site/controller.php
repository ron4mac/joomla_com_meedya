<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2019 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

class MeedyaController extends JControllerLegacy
{
	protected $uid = 0;
	protected $mnuItm;

	public function __construct ($config = array())
	{
		if (RJC_DBUG) { MeedyaHelper::log('MeedyaController'); }
		parent::__construct($config);
		$this->uid = JFactory::getUser()->get('id');
		$this->mnuItm = $this->input->getInt('Itemid', 0);
	}

	public function display ($cachable = false, $urlparams = false)
	{
	//	if (!$this->uid) {
	//		JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
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
		$this->setRedirect(JRoute::_('index.php?option=com_meedya&Itemid='.$this->mnuItm, false));
	}

}
