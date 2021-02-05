<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2021 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

class MeedyaControllerSearch extends JControllerLegacy
{
	protected $default_view = 'search';
	protected $mnuItm;

	public function __construct ($config = [])
	{
		if (RJC_DBUG) { MeedyaHelper::log('MeedyaControllerSearch'); }
		parent::__construct($config);
		$this->mnuItm = $this->input->getInt('Itemid', 0);
	}

	public function display ($cachable = false, $urlparams = false)
	{
		if (RJC_DBUG) { MeedyaHelper::log('MeedyaControllerSearch : display'); }
		$view = $this->getView('search','html');
		$view->itemId = $this->mnuItm;
		$view->aid = $this->input->post->getInt('aid', 0);
		$view->sterm = $this->input->post->getString('sterm', '');
		return parent::display($cachable, $urlparams);
	}

}
