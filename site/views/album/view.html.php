<?php
defined('_JEXEC') or die;

include_once JPATH_COMPONENT.'/views/meedyaview.php';

class MeedyaViewAlbum extends MeedyaView
{
	protected $aid;

	function display ($tpl = null)
	{
		$app = JFactory::getApplication();
		$this->state = $this->get('State');	//echo'<xmp>';var_dump($this->state);echo'</xmp>';	//echo get_class($this->state);
		$this->aid = $this->state->get('album.id');
		$this->items = $this->get('Items');
		$this->title = $this->get('Title');
		$this->albums = $this->get('Albums');

		if ($this->getLayout() == 'each') {
			$iid = $app->input->get->get('iid');
			$this->six = 0;
			$this->files = array();
			$m = $this->getModel();
			
//			$this->album = $m->getAlbum($this->aid);
			if ($this->items)
				foreach ($this->items as $item) {
					if ($item == $iid) $this->six = count($this->files);
					$this->files[] = $m->getItemFile($item);
				}
		} else {
//			$app->getPathWay()->addItem($this->get('Title'),'RRRRRR'/*.$aid*/);
			$this->pagination = $this->get('Pagination');
		}

		parent::display($tpl);
	}

	function __display ($tpl = null)
	{
		$this->state = $this->get('State');
		$this->aid = $this->state->get('album.id');
		$items = $this->get('Items');
		$m = $this->getModel();
		$this->album = $m->getAlbum($this->aid);
		if ($items)
			foreach ($items as $item)  {
				$this->slides[] = $m->getItemFile($item);
			}

		$this->html5slideshowCfg = $m->getCfg('ss');
		if (!$this->html5slideshowCfg) {
			$this->html5slideshowCfg = MeedyaHelper::$ssDefault;
		}
		$jawc = new JApplicationWebClient();
		if ($jawc->mobile) {
			$this->html5slideshowCfg['tT'] = 's';
		}

		parent::display($tpl);
	}

}