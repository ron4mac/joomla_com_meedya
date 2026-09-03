<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2023-2026 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.6.0
*/
namespace RJCreations\Component\Meedya\Site\View\Public;

use RJCreations\Component\Meedya\Site\Model\PublicModel;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use RJCreations\Component\Meedya\Site\View\MeedyaView;
use RJCreations\Component\Meedya\Site\Helper\MeedyaHelper;

class HtmlView extends MeedyaView
{
	public $isSearch;
	public $useFanCB;

	public $pathWay;
	public $title;
	public $desc;
	public $albums;

	protected $pgid;
	protected $aid;
	protected $ownername;

	public function __construct ($config = [])
	{
		if (RJC_DBUG) { MeedyaHelper::log('MeedyaViewPublic'); }
		parent::__construct($config);
		$this->pgid = $this->app->input->get('pgid','','cmd');
	}

	public function display ($tpl=null): void
	{
		$m = $this->getModel();
		$this->state = $m->getState();

		switch ($this->getLayout()) {
			case 'album':
				[$gdir, $gsfx, $this->aid] = explode('|', base64_decode($this->pgid));
				$this->isSearch = true;
				$this->useFanCB = true;
				$this->ownername = $m->getOwnerName($gdir);
				$pw = $this->app->getPathWay();
				$pw->setItemName(0, $this->params->get('page_title'));
				$pw->addItem($this->ownername, Route::_('index.php?option=com_meedya&view=public&layout=album&pgid='.$this->pgid.'&Itemid='.$this->itemId, false));

				$apw = $m->getAlbumPath();  //$m->getAlbumPath($this->aid);
				foreach ($apw as $ap) {
					foreach ($ap as $k => $v) {
						if ($k != $this->aid) {
							$pw->addItem($v[0], Route::_('index.php?option=com_meedya&view=public&layout=album&pgid='.$v[1].'&Itemid='.$this->itemId, false));
						}
					}
				}
				//$this->pathWay = [$this->params->get('page_title')];
				$this->pathWay = $pw->getPathway();
				$this->gallpath = $m->getGallpath();
				$this->title = $m->getTitle();
				$this->desc = $m->getDesc();
				$this->albums = $m->getAlbums();
				$this->items = $m->getAlbumItems();
				$this->params->set('owner', $this->ownername);
				break;
			default:
				if ($this->params->get('full_gallery', 0)) {
					$tpl = $this->pgid ? 'fuser' : 'full';
					$this->items = $this->pgid ? $m->getAlbums() : $m->getItems();
				} else {
					$this->items = $m->getItems();
				}
		}
		parent::display($tpl);
	}

	protected function getAlbumThumb ($albrec): string
	{
		$pics = $albrec->items ? explode('|', $albrec->items) : [];
		if (!$albrec->thumb) {
			$albrec->thumb = $pics !== [] ? $pics[0] : false;
		}
		if ($albrec->thumb) {
			$m = $this->getModel();
			$albrec->paix ??= false;
			$gallpath = $m->getGallpath($albrec->paix);
			return $gallpath.'/thm/'.$this->getItemThumbP($albrec->thumb, $albrec->paix);
		}
		return 'media/com_meedya/img/noimages.jpg';
	}

	protected function getItemThumbP ($iid, $paix)
	{
		$m = $this->getModel();
		return $m->getItemThumbFile($iid, $paix);
	}

}
