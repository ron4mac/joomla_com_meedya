<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

JLoader::register('JHtmlMeedya', JPATH_COMPONENT . '/helpers/html/meedya.php');

class MeedyaController extends JControllerLegacy
{
	protected $uid = 0;
	protected $mnuItm;

	public function __construct ($config = [])
	{
	//	if (RJC_DBUG) { MeedyaHelper::log('MeedyaController'); }
		parent::__construct($config);
		$this->uid = Factory::getUser()->get('id');
		$this->mnuItm = $this->input->getInt('Itemid', 0);
		if ($this->mnuItm) {
			Factory::getApplication()->setUserState('com_meedya.instance', $this->mnuItm.':'.MeedyaHelper::getInstanceID().':'.$this->uid);
		}
	}

/*
	public function display ($cachable = false, $urlparams = false)
	{
		if ($this->input->getString('view') == 'public') return parent::display($cachable, $urlparams);
		if (file_exists(MeedyaHelper::userDataPath($this->mnuItm))) {
			$view = $this->getView('meedya','html');
		} else {
			//set to a view that has no model
			$this->input->set('view', 'startup');
			$view = $this->getView('startup','html');
		}
		$view->itemId = $this->mnuItm;
		return parent::display($cachable, $urlparams);
	}
*/

	public function none ()
	{ // fail if there has been no specific task
		header("HTTP/1.0 500 NO TASK");
		echo 'No task was specified';
		jexit();
	}


}
