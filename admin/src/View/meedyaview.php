<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.4.0
*/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\View\HtmlView;

require_once JPATH_BASE . '/components/com_meedya/src/Helper/meedya.php';

/**
 * View class for a list of user schedules.
 */
class MeedyaView extends HtmlView
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display ($tpl = null)
	{
		HTMLHelper::stylesheet('administrator/components/com_meedya/static/meedya.css', ['version' => 'auto']);

		$this->items			= $this->get('Items');
		$this->pagination		= $this->get('Pagination');	//echo'@@@@'.($this->pagination?'YES':'NO').'@@@@';	//echo'<xmp>';var_dump($this->pagination);echo'</xmp>';
		$this->state			= $this->get('State');	//var_dump($this->state);
		$this->filterForm		= $this->get('FilterForm');
		$this->activeFilters	= $this->get('ActiveFilters');

		// Check for errors.
		//		if (count($errors = $this->get('Errors'))) {
		//			JError::raiseError(500, implode("\n", $errors));
		//			return false;
		//		}

		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar ()
	{
		$canDo	= MeedyaAdminHelper::getActions();

		JToolBarHelper::title(Text::_('COM_MEEDYA_MENU').': '.Text::_('COM_MEEDYA_MANAGER_'.strtoupper($this->relm)), 'stack meedya');

		JToolBarHelper::deleteList(Text::_('COM_MEEDYA_MANAGER_DELETEOK'));
		//JToolBarHelper::trash('usernotes.trash');

	//	if ($canDo->get('core.edit.state')) {
	//		JToolBarHelper::custom('notes.reset', 'refresh.png', 'refresh_f2.png', 'JUSERSCHED_RESET', false);
	//	}

		JToolBarHelper::custom('rebuildExpodt', 'wrench', '', 'Rebuild exposure dates');
		JToolBarHelper::custom('cleanOrphans', 'scissors', '', 'Clean orphan files');
		JToolBarHelper::custom('recalcStorage', 'database', '', 'Re-calculate storage');
		JToolBarHelper::custom('dbaseFixes', 'wand', '', 'Fix database issues');

		JToolBarHelper::divider();
		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_meedya');
		}
		JToolBarHelper::divider();
		JToolBarHelper::help('meedya_manage', true);
	}

	protected function state ($vari, $set=false, $val='', $glb=false)
	{
		$stvar = ($glb?'':'com_meedya.').$vari;
		$app = Factory::getApplication();
		if ($set) {
			$app->setUserState($stvar, $val);
			return;
		}
		return $app->getUserState($stvar, '');
	}

}
