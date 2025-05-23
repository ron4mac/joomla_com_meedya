<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2024 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.4.0
*/
namespace RJCreations\Component\Meedya\Site\View\Slides;

defined('_JEXEC') or die;

use Joomla\Application\Web\WebClient;
use RJCreations\Component\Meedya\Site\View\MeedyaView;
use RJCreations\Component\Meedya\Site\Helper\MeedyaHelper;


//require_once JPATH_BASE . '/components/com_meedya/src/View/MeedyaView.php';

class HtmlView extends MeedyaView
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
		$jawc = new WebClient();
		if (true || $jawc->mobile) {
			$this->html5slideshowCfg['tT'] = 'n';
		}

		parent::display($tpl);
	}

}
