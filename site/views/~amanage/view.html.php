<?php
defined('_JEXEC') or die;

include_once JPATH_COMPONENT.'/views/meedyaview.php';

no class MeedyaViewAManage extends MeedyaView
{
	protected $album;

	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$this->state = $this->get('State');
		$this->album = $this->get('Album');
		$this->aid = $this->state->get('album.id');
		$this->items = $this->get('Items');
		$app->getPathWay()->addItem($this->get('Title'),'RRRRRR'/*.$aid*/);
		$this->pagination = $this->get('Pagination');

		parent::display($tpl);
	}
}
