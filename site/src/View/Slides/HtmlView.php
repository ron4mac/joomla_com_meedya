<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2026 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.6.0
*/
namespace RJCreations\Component\Meedya\Site\View\Slides;

use RJCreations\Component\Meedya\Site\Model\SlidesModel;

defined('_JEXEC') or die;

use Joomla\Application\Web\WebClient;
use RJCreations\Component\Meedya\Site\View\MeedyaView;
use RJCreations\Component\Meedya\Site\Helper\MeedyaHelper;


//require_once JPATH_BASE . '/components/com_meedya/src/View/MeedyaView.php';

class HtmlView extends MeedyaView
{
	public $aid;
	public $html5slideshowCfg;
	protected $slides = [];
	protected $album;

	public function display ($tpl = null): void
	{
		$m = $this->getModel();
		$this->state = $m->getState();
		$this->aid = $this->state->get('album.id');
		$items = $m->getItems();
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
