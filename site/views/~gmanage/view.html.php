<?php
defined('_JEXEC') or die;

//include_once JPATH_COMPONENT.'/views/meedyaview.php';

class MeedyaViewGManage extends JViewLegacy		//MeedyaView
{
	protected $album;
	protected $isAdmin = true;

	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$this->state = $this->get('State');
		$this->html5slideshowCfg = array(
			'aA'=>1,	//slideshow action icon at album header
			'aT'=>1,	//shoehorn in this slideshow action at thumbs page
			'uA'=>1,	//user allow album settings (and their default)
			'nW'=>0,	//new (pop) window
			'pS'=>2,	//picture size (intermediate/full)
			'tT'=>'d',	//image transition = dissolve
			'vT'=>1,	//show Title in text area
			'vD'=>1,	//show Desc in title area
			'sI'=>0,	//shuffle slides for show
			'aP'=>1,	//autoplay
			'lS'=>0,	//loop slideshow
			'sD'=>5,	//slide duration
			'dC'=>'#666'	//control area background
				.',#CCC'	//control area text
				.',#333'	//text area background
				.',#FFF'	//text area text
				.',#000',	//pic area background
			'iS'=>'cb1' //iconset
		);

	//	$this->album = $this->get('Album');
	//	$this->aid = $this->state->get('album.id');
	//	$this->items = $this->get('Items');
	//	$app->getPathWay()->addItem($this->get('Title'),'RRRRRR'/*.$aid*/);
	//	$this->pagination = $this->get('Pagination');

		parent::display($tpl);
	}
}
