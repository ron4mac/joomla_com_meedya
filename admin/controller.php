<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

JLoader::register('MeedyaHelperDb', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/db.php');

require_once JPATH_COMPONENT.'/helpers/meedya.php';

class MeedyaController extends JControllerLegacy
{

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
		$sdp = MeedyaAdminHelper::getStorageBase();
		$cids = $this->input->get('cid',array(),'array');
		$view = $this->input->get('view');
		$tc = $view == 'meedya' ? '@' : '_';
		foreach ($cids as $cid) {
			MeedyaHelperDb::rebuildExpodt(JPATH_ROOT.'/'.$sdp.'/'.$tc.$cid.'/'.JApplicationHelper::getComponentName());
		}
		$this->setRedirect('index.php?option=com_meedya&view='.$view, Text::_('COM_MEEDYA_MSG_COMPLETE'));
	}

	public function cleanOrphans ()
	{
		$sdp = MeedyaAdminHelper::getStorageBase();
		$cids = $this->input->get('cid',array(),'array');
		$view = $this->input->get('view');
		$tc = $view == 'meedya' ? '@' : '_';
		foreach ($cids as $cid) {
			MeedyaHelperDb::cleanOrphans(JPATH_ROOT.'/'.$sdp.'/'.$tc.$cid.'/'.JApplicationHelper::getComponentName());
		}
		$this->setRedirect('index.php?option=com_meedya&view='.$view, Text::_('COM_MEEDYA_MSG_COMPLETE'));
	}

	public function recalcStorage ()
	{
		$sdp = MeedyaAdminHelper::getStorageBase();
		$cids = $this->input->get('cid',array(),'array');
		$view = $this->input->get('view');
		$tc = $view == 'meedya' ? '@' : '_';
		foreach ($cids as $cid) {
			MeedyaHelperDb::recalcStorage(JPATH_ROOT.'/'.$sdp.'/'.$tc.$cid.'/'.JApplicationHelper::getComponentName());
		}
		$this->setRedirect('index.php?option=com_meedya&view='.$view, Text::_('COM_MEEDYA_MSG_COMPLETE'));
	}

	public function dbaseFixes ()
	{
		$sdp = MeedyaAdminHelper::getStorageBase();
		$cids = $this->input->get('cid',array(),'array');
		$view = $this->input->get('view');
	//	$tc = $view == 'meedya' ? '@' : '_';
		$issues = 0;
		foreach ($cids as $cid) {
			list($sid, $mnu) = explode('.', $cid);
		//	MeedyaHelperDb::fixItemAlbums(JPATH_ROOT.'/'.$sdp.'/'.$cid.'/'.JApplicationHelper::getComponentName());
			try {
				$issues += MeedyaHelperDb::updateDatabase(JPATH_ROOT.'/'.$sdp.'/'.$sid.'/com_meedya_'.$mnu);
			} catch (Exception $e) {
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
		}
		if ($issues) {
			$this->setRedirect('index.php?option=com_meedya&view='.$view, Text::_('COM_MEEDYA_MSG_INCOMPLETE'), 'warning');
		} else {
			$this->setRedirect('index.php?option=com_meedya&view='.$view, Text::_('COM_MEEDYA_MSG_COMPLETE'), 'success');
		}
	}

}
