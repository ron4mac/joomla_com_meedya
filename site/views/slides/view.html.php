<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

include_once JPATH_COMPONENT.'/views/meedyaview.php';

class MeedyaViewSlides extends MeedyaView
{
	protected $slides = [];
	protected $album;

	function display ($tpl = null)
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
