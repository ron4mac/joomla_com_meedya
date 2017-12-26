<?php
defined('_JEXEC') or die;

include_once JPATH_COMPONENT.'/views/meedyaview.php';

class MeedyaViewAlbums extends MeedyaView
{
//	protected $aid;

	public function display ($tpl = null)
	{
		$app = JFactory::getApplication();
		$this->state = $this->get('State');	//echo'<xmp>';var_dump($this->state);echo'</xmp>';	//echo get_class($this->state);
//		$this->aid = $this->state->get('album.id');
		$this->items = $this->get('Items');
		$this->title = $this->get('Title');
//		$this->albums = $this->get('Albums');
//		$app->getPathWay()->addItem($this->get('Title'),'RRRRRR'/*.$aid*/);
		$this->pagination = $this->get('Pagination');

		parent::display($tpl);
	}

}