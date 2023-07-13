<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2023 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.3.2
*/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;

JLoader::register('JHtmlMeedya', JPATH_COMPONENT . '/helpers/html/meedya.php');

class MeedyaController extends JControllerLegacy
{
	protected $uid = 0;
	protected $mnuItm;

	public function __construct ($config = [])
	{
		parent::__construct($config);
		$this->uid = Factory::getUser()->get('id');
		$this->mnuItm = $this->input->getInt('Itemid', 0);
		if ($this->mnuItm) {
			Factory::getApplication()->setUserState('com_meedya.instance', $this->mnuItm.':'.MeedyaHelper::getInstanceID().':'.$this->uid);
		}
	}

	public function display ($cachable = false, $urlparams = false)
	{
		if ($this->input->getString('view') == 'public') return parent::display($cachable, $urlparams);
		if (file_exists(MeedyaHelper::userDataPath($this->mnuItm))) {
			$view = $this->getView('meedya','html');
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
		$udp = MeedyaHelper::userDataPath($this->mnuItm);
		mkdir($udp.'/img', 0777, true);
		mkdir($udp.'/thm', 0777, true);
		mkdir($udp.'/med', 0777, true);
		file_put_contents($udp.'/index.html', $htm);
		file_put_contents($udp.'/img/index.html', $htm);
		file_put_contents($udp.'/thm/index.html', $htm);
		file_put_contents($udp.'/med/index.html', $htm);
		$this->setRedirect(Route::_('index.php?option=com_meedya&Itemid='.$this->mnuItm, false));
	}

	// receive a rating vote
	public function rateItem ()
	{
		$this->tokenCheck();
		$m = $this->getModel('social');
		$iid = $this->input->getInt('iid', 0);
		$val = $this->input->getInt('val', 0);
		try {
			// return 0-100 (percent) for a 5 point rating system
			echo (int)($m->rate($iid, $val) * 20);
		} catch (Exception $e) {
				header('HTTP/1.1 404 Database Error');
				jexit($e->getMessage());
		}
	}

	// check to see whether already has been rated
	public function rateChk ()
	{
		$m = $this->getModel('social');
		$iid = $this->input->getInt('iid', 0);
		try {
			if ($m->rateChk($iid)) {
			//	header('HTTP/1.1 400 Duplicate Submission');
				jexit(Text::_('COM_MEEDYA_ALREADY_RATED'));
			}
		} catch (Exception $e) {
				header('HTTP/1.1 404 Database Error');
				jexit($e->getMessage());
		}
	}

	// get all the comments for an item
	public function getComments ()
	{
		$this->tokenCheck();
		$m = $this->getModel('social');
		$iid = $this->input->getInt('iid', 0);
		try {
			$comments = $m->getComments($iid);
			$html = [];
			foreach ($comments as $comment) {
				$html[] = '<div class="mycomment"><div>'.$comment['cmnt'].'</div>';
				$html[] = '<div class="mycommentn">'.Factory::getUser($comment['uid'])->name.'&nbsp;&nbsp;'.date(Text::_('DATE_FORMAT_LC5'),$comment['ctime']).'</div></div>';
			}
			echo implode("\n", $html);
		} catch (Exception $e) {
				header('HTTP/1.1 404 Database Error');
				jexit($e->getMessage());
		}
	}

	public function addComment ()
	{
		$this->tokenCheck();
		file_put_contents('COMSUB.txt', print_r($this->input->post, true));
		$iid = $this->input->post->getInt('iid', 0);
		$cmnt = $this->input->post->get('cmntext', '', 'string');
		$m = $this->getModel('social');
		echo '&nbsp;'.HTMLHelper::_('meedya.cmntsIcon').' '.$m->addComment($iid, $this->uid, $cmnt);
	}
/*
	public function picframekey ()
	{
		require_once JPATH_COMPONENT . '/classes/crypt.php';
		file_put_contents('COMSUB.txt', print_r($this->input->post, true));
		$parms = [];
		$title = $this->input->post->get('title', '', 'string');
		$parms['aid'] = $this->input->post->getInt('aid', 0);
		$parms['rcr'] = $this->input->post->getInt('recur', 0);
		$parms['obj'] = MeedyaHelper::getInstanceObject();

		$jparms = json_encode($parms);
		$sdly = $this->input->post->getInt('vtim', 30);
		$key = JUri::root().'?option=com_meedya&format=raw&task=picframe&key='.urlencode(\ComMeedya\Encryption::encrypt($jparms, $this->app->get('secret')));
		echo json_encode(['key'=>base64_encode($key),'title'=>base64_encode($title),'pcnt'=>0,'sdly'=>$sdly]);
	}

	public function picframe ()
	{
		header('Access-Control-Allow-Origin: *');
		require_once JPATH_COMPONENT . '/classes/crypt.php';
		$key = $this->input->get->get('key', '', 'base64');
		$data = \ComMeedya\Encryption::decrypt($key, $this->app->get('secret'));
		$prms = json_decode($data);
		
		$m = $this->getModel('picframe');
		$pics = $m->getPlayList($prms->aid,$prms->rcr,$prms->obj);
		echo "\t\t\t\t" . count($pics) . "\t" . implode("\n",$pics);
	}

	public function p4f ()
	{
		$file = 'test2.jpeg';
	//	$this->app->allowCache(true);
//		$this->app->clearHeaders()
//			->setHeader('Content-Type','image/jpeg; charset=utf-8',true)
//			->setHeader('Content-Length',(string)filesize($file),true)
//			->sendHeaders();
	//	header('Content-Type: image/jpeg');
	//	header('Content-Length: ' . filesize($file));
		echo '+_+_+_+_+_+_+';
		readfile($file);
	}
*/
	private function tokenCheck ()
	{
		if (!Session::checkToken()) {
			header('HTTP/1.1 403 Not Allowed');
			jexit(Text::_('JINVALID_TOKEN'));
		}
	}


}
