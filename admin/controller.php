<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2024 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.3.6
*/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Application\ApplicationHelper;

JLoader::register('MeedyaHelperDb', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/db.php');

require_once JPATH_COMPONENT.'/helpers/meedya.php';

class MeedyaController extends BaseController
{
	protected $storBase = '';

	public function __construct ($config = [], MVCFactoryInterface $factory = null, $app = null, $input = null)
	{
		$this->storBase = RJUserCom::getStorageBase();
		parent::__construct($config, $factory, $app, $input);
	}

	public function display ($cachable = false, $urlparams = false)
	{
		// Load the submenu.
	//	MeedyaHelper::addSubmenu($this->input->getCmd('view', 'meedya'));

		$view = $this->input->getCmd('view', 'meedya');
		$layout = $this->input->getCmd('layout', 'default');
		$id = $this->input->getInt('id');

		// Check for edit form.
		if ($view == 'meedyaitem' && $layout == 'edit' && !$this->checkEditId('com_meedya.edit.meedyaitem', $id)) {
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_meedya&view=meedya', false));

			return false;
		}

		parent::display($cachable, $urlparams);

		return $this;
	}

	public function rebuildExpodt ()
	{
		$this->tokenCheck();
		$cids = $this->input->get('cid',array(),'array');
		$view = $this->input->get('view');
//		$tc = $view == 'meedya' ? '@' : '_';
		foreach ($cids as $cid) {
			list($uid,$iid) = explode('|', $cid);
			$mid = $iid ? ('_'.$iid) : '';
			MeedyaHelperDb::rebuildExpodt(JPATH_ROOT.'/'.$this->storBase.'/'.$uid.'/'.ApplicationHelper::getComponentName().$mid);
		}
		$this->setRedirect('index.php?option=com_meedya&view='.$view, Text::_('COM_MEEDYA_MSG_COMPLETE'));
	}

	public function cleanOrphans ()
	{
		$this->tokenCheck();
		$cids = $this->input->get('cid',array(),'array');
		$view = $this->input->get('view');
//		$tc = $view == 'meedya' ? '@' : '_';
		foreach ($cids as $cid) {
			list($uid,$iid) = explode('|', $cid);
			$mid = $iid ? ('_'.$iid) : '';
			MeedyaHelperDb::cleanOrphans(JPATH_ROOT.'/'.$this->storBase.'/'.$uid.'/'.ApplicationHelper::getComponentName().$mid);
		}
		$this->setRedirect('index.php?option=com_meedya&view='.$view, Text::_('COM_MEEDYA_MSG_COMPLETE'));
	}

	public function recalcStorage ()
	{
		$this->tokenCheck();
		$cids = $this->input->get('cid',array(),'array');
		$view = $this->input->get('view');
//		$tc = $view == 'meedya' ? '@' : '_';
		foreach ($cids as $cid) {
			list($uid,$iid) = explode('|', $cid);
			$mid = $iid ? ('_'.$iid) : '';
			MeedyaHelperDb::recalcStorage(JPATH_ROOT.'/'.$this->storBase.'/'.$uid.'/'.ApplicationHelper::getComponentName().$mid);
		}
		$this->setRedirect('index.php?option=com_meedya&view='.$view, Text::_('COM_MEEDYA_MSG_COMPLETE'));
	}

	public function dbaseFixes ()
	{
		$this->tokenCheck();
		$cids = $this->input->get('cid',array(),'array');
		$view = $this->input->get('view');
		$msgs = [];
		foreach ($cids as $cid) {
			list($uid,$iid) = explode('|', $cid);
			$mid = $iid ? ('_'.$iid) : '';
			$msgs += RJUserCom::updateDb(JPATH_ROOT.'/'.$this->storBase.'/'.$uid.'/'.ApplicationHelper::getComponentName().$mid.'/meedya.db3');
		}
		if ($msgs) {
			$msg = Text::_('COM_MEEDYA_DBUP_ISSUE').($msgs ? '<br>'.implode('<br>',$msgs) : '');
			$notice = 'warning';
		} else {
			$msg = Text::_('COM_MEEDYA_DBUP_DONE');
			$notice = 'success';
		}
		$this->setRedirect('index.php?option=com_meedya&view='.$view, $msg, $notice);
	}

	private function tokenCheck ()
	{
		if (!Session::checkToken()) {
			header('HTTP/1.1 403 Not Allowed');
			jexit(Text::_('JINVALID_TOKEN'));
		}
	}

}
