<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2024 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.4.0
*/
namespace RJCreations\Component\Meedya\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

define('RJC_DBUG', (true || JDEBUG) && file_exists(JPATH_ROOT.'/rjcdev.php'));

class SearchController extends BaseController
{
	protected $default_view = 'search';
	protected $mnuItm;

	public function __construct ($config = [], $factory = null, $app = null, $input = null)
	{
		parent::__construct($config, $factory, $app, $input);
		$this->mnuItm = $this->input->getInt('Itemid', 0);
	}

	public function search ()
	{
		$sterm = $this->input->post->getString('sterm', '');
		$sterm = str_replace('#','\#',$sterm);
		if (@preg_match('#'.$sterm.'#', null)===false) {
			Factory::getApplication()->enqueueMessage(Text::_('COM_MEEDYA_INVALID_SEARCH'), 'error');
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
