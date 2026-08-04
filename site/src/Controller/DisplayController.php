<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2026 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.5.0
*/
namespace RJCreations\Component\Meedya\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\MVC\Controller\BaseController;
use RJCreations\Library\RJUserCom;
use RJCreations\Component\Meedya\Site\Helper\MeedyaHelper;

define('RJC_DBUG', (JDEBUG) && file_exists(JPATH_ROOT.'/rjcdev.php'));

class DisplayController extends BaseController
{
	protected $uid = 0;
	protected $mnuItm;
	protected $shrk = null;
	protected $instObj = null;

	public function __construct ($config = [], $factory = null, $app = null, $input = null)
	{
		parent::__construct($config, $factory, $app, $input);
		$this->uid = Factory::getUser()->get('id');
		$key = base64_decode($this->input->get->get('key', '', 'base64'));
		if ($key) {
			$data = MeedyaHelper::decodeKey($key);
			$prms = json_decode($data);
			$config['inst'] = $prms;
			$this->shrk = $prms;
			$this->instObj = $prms->obj;
		}
		$this->mnuItm = $this->input->getInt('Itemid', 0);
	}

	public function display ($cachable = false, $urlparams = []): DisplayController
	{
		if (in_array($this->input->getString('view'),['picframe','public'])) return parent::display($cachable, $urlparams);
		if (file_exists(RJUserCom::getStoragePath($this->instObj))) {
			$view = $this->getView('meedya','html','site',$this->shrk ? ['inst'=>$this->shrk] : null);
		} else {
			//set to a view that has no model
			$this->input->set('view', 'startup');
			$view = $this->getView('startup','html');
		}
		$view->itemId = $this->mnuItm;
		return parent::display($cachable, $urlparams);
	}

	public function begin ()
	{
		if (!$this->uid) return;
		$htm = '<!DOCTYPE html><title></title>';
		$udp = RJUserCom::getStoragePath();
		mkdir($udp.'/img', 0777, true);
		mkdir($udp.'/thm', 0777, true);
		mkdir($udp.'/med', 0777, true);
		file_put_contents($udp.'/index.html', $htm);
		file_put_contents($udp.'/img/index.html', $htm);
		file_put_contents($udp.'/thm/index.html', $htm);
		file_put_contents($udp.'/med/index.html', $htm);
		$this->setRedirect(Route::_('index.php?option=com_meedya&Itemid='.$this->mnuItm, false));
	}

	// provide an instance object to the model when the request if from a shared album link						*** can probably use what was gathered in __construct above
	public function getModel($name = '', $prefix = '', $config = array())
	{
		if ($name == 'album') {
			$key = base64_decode($this->input->get->get('key', '', 'base64'));
			if ($key) {
				$data = MeedyaHelper::decodeKey($key);
				$prms = json_decode($data);
				$config['inst'] = $prms;
				$this->instObj = $prms->obj;
			}
		}
		return parent::getModel($name, $prefix, $config);
	}

}
