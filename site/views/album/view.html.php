<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2017 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

include_once JPATH_COMPONENT.'/views/meedyaview.php';

class MeedyaViewAlbum extends MeedyaView
{
	protected $aid;

	public function __construct ($config = array())
	{
		if (RJC_DBUG) { MeedyaHelper::log('MeedyaViewAlbum'); }
		parent::__construct($config);
	}

	function display ($tpl = null)
	{
		$app = JFactory::getApplication();
		$this->state = $this->get('State');	//echo'<xmp>';var_dump($this->state);echo'</xmp>';	//echo get_class($this->state);
		$this->aid = $this->state->get('album.id');
		$this->items = $this->get('Items');
		$this->title = $this->get('Title');
		$this->albums = $this->get('Albums');
		$m = $this->getModel();

		if ($this->getLayout() == 'each') {
			$iid = $app->input->get->get('iid');
			$this->six = 0;
			$this->files = array();
			
//			$this->album = $m->getAlbum($this->aid);
			if ($this->items)
				foreach ($this->items as $item) {
					if ($item == $iid) $this->six = count($this->files);
					$this->files[] = $m->getItemFile($item);
				}
		} else {
			$pw = $app->getPathWay();
			$pw->setItemName(0, '<i class="icon-home-2" title="Gallery Home"></i>');
			$apw = $m->getAlbumPath($this->aid);
			foreach ($apw as $ap) {
				foreach ($ap as $k => $v) {
					if ($k != $this->aid) {
						$pw->addItem($v, JRoute::_('index.php?option=com_meedya&view=album&aid='.$k, false));
					}
				}
			}
		//	$app->getPathWay()->addItem($this->get('Title'),'RRRRRR'.$this->aid);
			$this->pathWay = $pw->getPathway();
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