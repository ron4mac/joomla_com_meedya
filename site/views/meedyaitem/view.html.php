<?php
defined('_JEXEC') or die;

class MeedyaViewMeedyaItem extends JViewLegacy
{
	protected $state;
	protected $item;

	function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		$params		= $app->getParams();

		// Get some data from the models
		$state		= $this->get('State');
		$item		= $this->get('Item');
		$category	= $this->get('Category');

		if ($this->getLayout() == 'edit') {
			$this->_displayEdit($tpl);
			return;
		}

		if ($item->url) {
			// redirects to url if matching id found
			$app->redirect($item->url);
		} else {
			//TODO create proper error handling
			$app->redirect(JRoute::_('index.php'), JText::_('COM_MEEDYA_ERROR_MEEDYAITEM_NOT_FOUND'), 'notice');
		}
	}
}
