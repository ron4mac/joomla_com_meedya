<?php
defined('_JEXEC') or die;

include_once JPATH_COMPONENT.'/views/meedyaview.php';

class MeedyaViewMeedya extends MeedyaView
{
	protected $manage = 1;

	function display ($tpl = null)
	{
		$this->state = $this->get('State');
		$this->items = $this->get('Items');

		parent::display($tpl);
	}

}