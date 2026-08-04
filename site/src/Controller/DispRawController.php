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
//use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
//use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Uri\Uri;
use RJCreations\Library\RJUserCom;
use RJCreations\Component\Meedya\Site\Helper\HtmlMeedya;
use RJCreations\Component\Meedya\Site\Helper\MeedyaHelper;

include JPATH_COMPONENT.'/lpf.php';
if (!defined('PFDW')) {
	define('PFDW', 1024);
	define('PFDH', 600);
	define('IMGBKG', 'bgi3.jpeg');
}

class DispRawController extends BaseController
{
//	public function __construct ($config = [])
//	{
//		error_reporting(0);
//		parent::__construct($config);
//		include JPATH_COMPONENT.'/lpf.php';
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
		file_put_contents('COMSUB.txt', print_r($this->input->post, true));
		$parms = [];
		$title = $this->input->post->get('title', '', 'string');
		$parms['aid'] = $this->input->post->getInt('aid', 0);
		$parms['rcr'] = $this->input->post->getInt('recur', 0);
		$parms['obj'] = RJUserCom::getInstObject();

		$jparms = json_encode($parms);
		$sdly = $this->input->post->getInt('vtim', 30);
		$key = Uri::root().'?option=com_meedya&format=raw&task=picframe&key='.urlencode(MeedyaHelper::encodeKey($jparms));
		echo json_encode(['key'=>base64_encode($key),'title'=>base64_encode($title),'pcnt'=>0,'sdly'=>$sdly]);
	}


	public function picframe ()
	{
		header('Access-Control-Allow-Origin: *');
		$key = $this->input->get->get('key', '', 'base64');
		$data = MeedyaHelper::decodeKey($key);
		$prms = json_decode($data);
		if (empty($prms->rcr)) $prms->rcr = 0;

		$m = $this->getModel('picframe','',['inst'=>$prms->obj]);
		if ($this->input->get->get('act', '', 'string')=='thms') {
			$thms = $m->getThumbnails($prms->aid,$prms->rcr,$prms->obj);
			foreach ($thms as $thm) {
				echo '<img class="pfthm" src="'.$thm['src'].'" data-iid="'.$thm['iid'].'">';
			}
		} elseif ($this->input->getInt('pco', 0)) {
			echo $m->getPlayListCount($prms->aid,$prms->rcr,$prms->obj);
		} else {
			$pics = $m->getPlayList($prms->aid,$prms->rcr,$prms->obj);
			echo "\t\t\t\t" . count($pics) . "\t" . implode("\n",$pics);
		}
	}


	// call from the picframe for an individual image
	public function p4f ()
	{
		list($key,$pid) = explode('.',$this->input->get->get('p', '', 'string'));
		$dim = array_map('intval', explode('x',$this->input->get->get('ddim', PFDW.'x'.PFDH, 'string')));
		file_put_contents('COMSUB.txt', print_r($dim, true), FILE_APPEND);
		$inst = json_decode(base64_decode($key));
		$m = $this->getModel('picframe','',['inst'=>$inst]);
		
		$file = $m->getFramePic($pid);

		//file_put_contents('OB.txt', print_r(ob_get_status(true), true), FILE_APPEND);
		// try to clear any php notification messages that would corrupt the image data
		// will likely only work with buffering set for php 
		@ob_end_clean();@ob_end_clean();

		$this->app->clearHeaders()
			->setHeader('Content-Type','image/jpeg',true);
		//	->setHeader('Content-Length',(string)filesize($file),true);
//			->sendHeaders();
	//	header('Content-Type: image/jpeg');
	//	header('Content-Length: ' . filesize($file));
	//	echo '+_+_+_+_+_+_+';
	//	readfile($file);
	header('Content-Type: image/jpeg; charset=utf-8',true);
		//$this->makeFimg($file, $dim);
		$this->resizeAndCenterImage($file, $dim[0], $dim[1]);
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

	private function newImg ($new_w, $new_h)
	{
		if (function_exists('imagecreatetruecolor')) return imagecreatetruecolor($new_w, $new_h);
		return imagecreate($new_w, $new_h);
	}

	private function createImage ($new_w, $new_h, $matte)
	{
		if ($matte) {
			$m = imagecreatefromjpeg(JPATH_COMPONENT.'/static/img/'.$matte);
			$im = $this->newImg($new_w, $new_h);
			$result = imagecopyresampled($im, $m, 0, 0, 0, 0, $new_w, $new_h, imagesx($m), imagesy($m));
			if (!$result) {
				$result = @imagecopyresized($im, $m, 0, 0, 0, 0, $new_w, $new_h, $new_w, $new_h);
			}
			return $im;
		}
		return $this->newImg($new_w, $new_h);
	}

	private function makeFimg ($simg, $dim)
	{
		list($w,$h,$t) = getimagesize($simg);
		list($dimW,$dimH) = $dim;
		list($nw,$nh,$x,$y) = $h>$w ? $this->inFrameRect($w,$h,$dimW,$dimH) : $this->frameRect($w,$h,$dimW,$dimH);
		$src_img = $this->getimgRes($simg, $t);
		$dst_img = $this->createImage($dimW, $dimH, $h>$w ? IMGBKG : null);

		if ($h>$w) {
			$result = imagecopyresampled($dst_img, $src_img, $x, $y, 0, 0, $nw, $nh, $w, $h);
			if (!$result) {
				$result = @imagecopyresized($dst_img, $src_img, $x, $y, 0, 0, $nw, $nh, $w, $h);
			}
		} else {
			$result = imagecopyresampled($dst_img, $src_img, 0, 0, $x, $y, $dimW, $dimH, $nw, $nh);
			if (!$result) {
				$result = @imagecopyresized($dst_img, $src_img, 0, 0, $x, $y, $dimW, $dimH, $nw, $nh);
			}
		}
		imagejpeg($dst_img, null, 90);
	}

	private function resizeAndCenterImage (string $sourcePath, int $boxWidth, int $boxHeight): bool
	{
		// 1. Get original dimensions and image type
		$imageInfo = getimagesize($sourcePath);
		if (!$imageInfo) {
			return false;
		}
		list($origWidth, $origHeight, $imageType) = $imageInfo;

		// 2. Load the source image based on type
		switch ($imageType) {
			case IMAGETYPE_JPEG:
				$sourceImage = imagecreatefromjpeg($sourcePath);
				break;
			case IMAGETYPE_PNG:
				$sourceImage = imagecreatefrompng($sourcePath);
				break;
			case IMAGETYPE_GIF:
				$sourceImage = imagecreatefromgif($sourcePath);
				break;
			case IMAGETYPE_WEBP:
				$sourceImage = imagecreatefromwebp($sourcePath);
				break;
			default:
				return false; // Unsupported format
		}
		if (!$sourceImage) {
			return false;
		}

		// 3. Calculate scaling factor (take the smaller factor so it fits completely inside)
		$scale = min($boxWidth / $origWidth, $boxHeight / $origHeight);
		// Calculate new scaled dimensions
		$newWidth  = (int) round($origWidth * $scale);
		$newHeight = (int) round($origHeight * $scale);

		// 4. Calculate offset to center the scaled image inside the bounding box
		$dstX = (int) floor(($boxWidth - $newWidth) / 2);
		$dstY = (int) floor(($boxHeight - $newHeight) / 2);

		// 5. Create the canvas matching the exact prescribed box size, using the background image
		$canvas = $this->createImage($boxWidth, $boxHeight, $origHeight>$origWidth ? IMGBKG : IMGBKG);			// null);

		// 6. Resample and copy the image into the center position
		imagecopyresampled(
			$canvas,
			$sourceImage,
			$dstX, $dstY,			// Destination X, Y (centered offsets)
			0, 0,					// Source X, Y
			$newWidth, $newHeight,	// Destination target width, height
			$origWidth, $origHeight	// Source width, height
		);

		// 7. Send output image (defaults to JPEG here, adjust as needed)
		$success = imagejpeg($canvas, null, 90);

		// 8. Clean up memory
		imagedestroy($sourceImage);
		imagedestroy($canvas);

		return $success;
	}

}
