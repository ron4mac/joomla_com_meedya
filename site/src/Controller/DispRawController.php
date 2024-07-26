<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2024 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.4.0
*/
namespace RJCreations\Component\Meedya\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
//use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
//use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Uri\Uri;
use RJCreations\Library\RJUserCom;
use RJCreations\Component\Meedya\Site\Helper\HtmlMeedya;

define('PFDW', 1024);
define('PFDH', 600);

class DispRawController extends BaseController
{
//	public function __construct ($config = [])
//	{
//		error_reporting(0);
//		parent::__construct($config);
//	}

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
		//$this->tokenCheck();
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
		echo '&nbsp;'.HtmlMeedya::cmntsIcon(true).' '.$m->addComment($iid, RJUserCom::getInstObject()->uid, $cmnt);
	}


	public function picframekey ()
	{
		require_once JPATH_COMPONENT . '/classes/crypt.php';
		file_put_contents('COMSUB.txt', print_r($this->input->post, true));
		$parms = [];
		$title = $this->input->post->get('title', '', 'string');
		$parms['aid'] = $this->input->post->getInt('aid', 0);
		$parms['rcr'] = $this->input->post->getInt('recur', 0);
		$parms['obj'] = RJUserCom::getInstObject();

		$jparms = json_encode($parms);
		$sdly = $this->input->post->getInt('vtim', 30);
		$key = Uri::root().'?option=com_meedya&format=raw&task=picframe&key='.urlencode(\ComMeedya\Encryption::encrypt($jparms, $this->app->get('secret')));
		echo json_encode(['key'=>base64_encode($key),'title'=>base64_encode($title),'pcnt'=>0,'sdly'=>$sdly]);
	}


	public function picframe ()
	{
		header('Access-Control-Allow-Origin: *');
		require_once JPATH_COMPONENT . '/classes/crypt.php';
		$key = $this->input->get->get('key', '', 'base64');
		$data = \ComMeedya\Encryption::decrypt($key, $this->app->get('secret'));
		$prms = json_decode($data);
		if (empty($prms->rcr)) $prms->rcr = 0;
		
		$m = $this->getModel('picframe','',['inst'=>$prms->obj]);
		if ($this->input->get->get('act', '', 'string')=='thms') {
			$thms = $m->getThumbnails($prms->aid,$prms->rcr,$prms->obj);
			foreach ($thms as $thm) {
				echo '<img class="pfthm" src="'.$thm['src'].'" data-iid="'.$thm['iid'].'">';
			}
		} else {
			$pics = $m->getPlayList($prms->aid,$prms->rcr,$prms->obj);
			echo "\t\t\t\t" . count($pics) . "\t" . implode("\n",$pics);
		}
	}


	// call from the picframe for an individual image
	public function p4f ()
	{
		list($key,$pid) = explode('.',$this->input->get->get('p', '', 'string'));
		$inst = json_decode(base64_decode($key));
		$m = $this->getModel('picframe','',['inst'=>$inst]);
		
		$file = $m->getFramePic($pid);

		//file_put_contents('OB.txt', print_r(ob_get_status(true), true), FILE_APPEND);
		// try to clear any php notification messages that would corrupt the image data
		// will likely only work with buffering set for php 
		@ob_end_clean();@ob_end_clean();

		$this->app->clearHeaders()
			->setHeader('Content-Type','image/jpeg; charset=utf-8',true);
		//	->setHeader('Content-Length',(string)filesize($file),true);
//			->sendHeaders();
	//	header('Content-Type: image/jpeg');
	//	header('Content-Length: ' . filesize($file));
	//	echo '+_+_+_+_+_+_+';
	//	readfile($file);
		$this->makeFimg($file);
	}


	private function tokenCheck ()
	{
		if (!Session::checkToken()) {
			header('HTTP/1.1 403 Not Allowed');
			jexit(Text::_('JINVALID_TOKEN'));
		}
	}

	// size to completely fill the destination rect
	private function frameRect ($sW, $sH, $dW, $dH)
	{
		// get the size ratio for each
		$sar = $sW/$sH;
		$dar = $dW/$dH;
		// default to perfect fit
		$fW = $sW;
		$fH = $sH;
		$x = 0;
		$y = 0;
	
		if ($dar>$sar) {
			$fH = round($sW/$dar);
			$y = ($sH-$fH)>>1;
		}
		if ($sar>$dar) {
			$fW = round($sH*$dar);
			$x = ($sW-$fW)>>1;
		}
		return [$fW, $fH, $x, $y];
	}

	// size to fit completely in rect .. portrait in landscape here
	private function inFrameRect ($sW, $sH, $dW, $dH)
	{
		// get the size ratio for each
		$sar = $sW/$sH;
		$dar = $dW/$dH;
		$fH = $dH;
		$fW = round($sW*$dH/$sH);
		$x = ($dW-$fW)>>1;

		return [$fW, $fH, $x, 0];
	}
	
	private function getimgRes ($name, $type)
	{
		switch ($type) {
			case 1:
				$im = imagecreatefromgif($name);
				break;
			case 2:
				$im = imagecreatefromjpeg($name);
				break;
			case 3:
				$im = imagecreatefrompng($name);
				break;
			}
		return $im;
	}
	
	private function createImage ($new_w, $new_h, $matte)
	{
		if ($matte) {
			return imagecreatefromjpeg(JPATH_COMPONENT.'/static/img/'.$matte);
		}
		if (function_exists('imagecreatetruecolor')) {
			$retval = imagecreatetruecolor($new_w, $new_h);
		}
	
		if (!$retval) {
			$retval = imagecreate($new_w, $new_h);
		}
	
		return $retval;
	}
	
	private function makeFimg ($simg)
	{
		list($w,$h,$t) = getimagesize($simg);
		list($nw,$nh,$x,$y) = $h>$w ? $this->inFrameRect($w,$h,PFDW,PFDH) : $this->frameRect($w,$h,PFDW,PFDH);
		$src_img = $this->getimgRes($simg, $t);
		$dst_img = $this->createImage(PFDW, PFDH, $h>$w ? 'bgi3.jpeg' : null);

		if ($h>$w) {
			$result = imagecopyresampled($dst_img, $src_img, $x, $y, 0, 0, $nw, $nh, $w, $h);
			if (!$result) {
				$result = @imagecopyresized($dst_img, $src_img, $x, $y, 0, 0, $nw, $nh, $w, $h);
			}
		} else {
			$result = imagecopyresampled($dst_img, $src_img, 0, 0, $x, $y, PFDW, PFDH, $nw, $nh);
			if (!$result) {
				$result = @imagecopyresized($dst_img, $src_img, 0, 0, $x, $y, PFDW, PFDH, $nw, $nh);
			}
		}
		imagejpeg($dst_img, null, 90);
	}

}
