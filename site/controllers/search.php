<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2021 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class MeedyaControllerSearch extends JControllerLegacy
{
	protected $default_view = 'search';
	protected $mnuItm;

	public function __construct ($config = [])
	{
	//	if (RJC_DBUG) MeedyaHelper::log('MeedyaControllerSearch');
		parent::__construct($config);
		$this->mnuItm = $this->input->getInt('Itemid', 0);
	}

	public function search ()
	{
		$sterm = $this->input->post->getString('sterm', '');
		$sterm = str_replace('#','\#',$sterm);
		if (@preg_match('#'.$sterm.'#', null)===false) {
			Factory::getApplication()->enqueueMessage(JText::_('COM_MEEDYA_INVALID_SEARCH'), 'error');
			$this->setRedirect($this->input->server->getString('HTTP_REFERER', 'index.php?Itemid='.$this->mnuItm));
		} else {
			$view = $this->getView('search','html');
			$view->setModel($this->getModel('search'), true);
			$view->itemId = $this->mnuItm;
			$view->aid = $this->input->post->getInt('aid', 0);
			$view->sterm = $sterm;
			$view->display();
		}
	}

}
