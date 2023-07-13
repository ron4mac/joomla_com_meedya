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

define('PFDW', 1024);
define('PFDH', 600);

class MeedyaController extends JControllerLegacy
{
	public function __construct ($config = [])
	{
		error_reporting(0);
		parent::__construct($config);
	}


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
		
		$m = $this->getModel('picframe','',['inst'=>$prms->obj]);
		$pics = $m->getPlayList($prms->aid,$prms->rcr,$prms->obj);
		echo "\t\t\t\t" . count($pics) . "\t" . implode("\n",$pics);
	}


	public function p4f ()
	{
		list($key,$pid) = explode('.',$this->input->get->get('p', '', 'string'));
		$inst = json_decode(base64_decode($key));
		$m = $this->getModel('picframe','',['inst'=>$inst]);
		
		$file = 'test2.jpeg';
		$file = $m->getFramePic($pid);
	//	$this->app->allowCache(true);
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


	private function frameRect ($sW, $sH, $dW, $dH)
	{
		// get the size ratio for each
		$sar = $sW/$sH;
		$dar = $dW/$dH;
		// default to perfect fit
		$fW = $sW;
		$fH = $sH;
		$x = 0;
		$y =0;
	
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
		list($nw,$nh,$x,$y) = $h>$w
			? $this->inFrameRect($w,$h,PFDW,PFDH)
			: $this->frameRect($w,$h,PFDW,PFDH);
	//	echo "$w,$h : $nw,$nh,$x,$y<br>";
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
