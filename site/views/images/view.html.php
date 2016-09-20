<?php
defined('_JEXEC') or die;

include_once JPATH_COMPONENT.'/views/meedyaview.php';

class MeedyaViewImages extends MeedyaView
{
	function display ($tpl = null)
	{
		$this->iids = $this->get('Items');
//		$this->items = $this->get('Items');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		parent::display($tpl);
	}
}
