<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2016 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

abstract class MeedyaHelperGraphics
{
	public static function createThumb ($src, $dest, $ext, $maxW=0, $maxH=100, $sqr=true)
	{
		try {
			$imgk = new Imagick(realpath($src));
			$imgk->readImage($src);
			$imgk->cropThumbnailImage(120, 120);
			$imgk->setImageFormat('JPG');
			$imgk->writeImage($dest.$ext);
			return filesize($dest.$ext);
		}
		catch(Exception $e) {
			die('Error when creating thumbnail: ' . $e->getMessage());
		}
	}

	public static function createMedium ($src, $dest, $ext, $maxW=0, $maxH=1200)
	{
		try {
			$imgk = new Imagick(realpath($src));
			$imgk->readImage($src);
			$imgk->scaleImage($maxW, $maxH);
			$imgk->writeImage($dest.$ext);
			return filesize($dest.$ext);
		}
		catch(Exception $e) {
			die('Error when creating medium image: ' . $e->getMessage());
		}
	}

	public static function orientImage ($src, $dest)
	{
		$flp = 0; $rot = 0;
		$osize = filesize(realpath($src));
		$exif = @exif_read_data(realpath($src));		//file_put_contents('exif.txt', print_r($exif,true), FILE_APPEND);
		if (!$exif) return;
		$ort = $exif['Orientation'];
		switch ($ort) {
			case 1: // nothing
				break;
			case 2: // horizontal flip
				$flp = 1;
				break;
			case 3: // 180 rotate left
				$rot = 180;
				break;
			case 4: // vertical flip
				$flp = 2;
				break;
			case 5: // vertical flip + 90 rotate right
				$flp = 2;
				$rot = 90;
				break;
			case 6: // 90 rotate right
				$rot = 90;
				break;
			case 7: // horizontal flip + 90 rotate right
				$flp = 1;
				$rot = 90;
				break;
			case 8: // 90 rotate left
				$rot = -90;
				break;
		}
		if (($flp + $rot) !== 0) {
			try {
				$imgk = new Imagick(realpath($src));
				if ($flp==1) { $imgk->flipImage(); }
				else if ($flp==2) { $imgk->flopImage(); }
				if ($rot!==0) { $imgk->rotateImage(new ImagickPixel('#00000000'), $rot); }
				$imgk->setImageOrientation(imagick::ORIENTATION_TOPLEFT);
				$imgk->writeImage(realpath($dest));
				return filesize(realpath($dest)) - $osize;
			}
			catch(Exception $e) {
				die('Error when orienting image: ' . $e->getMessage());
			}
		}
		return 0;
	}
}

include_once 'imgproc.php';

class ImageProcessor extends ImageProc
{
	protected $errs = array();
	protected $src;
	protected $imgk;

	public function __construct ($src)
	{
		parent::__construct($src);

		if (RJC_DBUG) { MeedyaHelper::log('IMimageProc'); }
		try {
			$this->src = $src;
			$this->imgk = new Imagick(realpath($src));
		//	$this->imgk->readImage($src);
		}
		catch(Exception $e) {
		//	die('Error getting image: ' . $e->getMessage());
			$this->errs[] = 'Error getting image: ' . $e->getMessage();
		}
	}

	public function getErrors ()
	{
		return $this->errs;
	}

	public function createThumb ($dest, $ext, $maxW=0, $maxH=100, $sqr=true)
	{
		try {
			$this->imgk->cropThumbnailImage(120, 120);
			$this->imgk->setImageFormat('JPG');
			$this->imgk->writeImage($dest.$ext);
			return filesize($dest.$ext);
		}
		catch(Exception $e) {
		//	die('Error when creating a thumbnail: ' . $e->getMessage());
			$this->errs[] = 'Error when creating thumbnail: ' . $e->getMessage();
		}
	}

	public function createMedium ($dest, $ext, $maxW=0, $maxH=1200)
	{
		try {
			$this->imgk->scaleImage($maxW, $maxH);
			$this->imgk->writeImage($dest.$ext);
			return filesize($dest.$ext);
		}
		catch(Exception $e) {
		//	die('Error when creating medium image: ' . $e->getMessage());
			$this->errs[] = 'Error when creating medium image: ' . $e->getMessage();
		}
	}

	public function orientImage ($dest)
	{
		$flp = 0; $rot = 0;
		$osize = filesize(realpath($this->src));
		$exif = @exif_read_data(realpath($this->src));		//file_put_contents('exif.txt', print_r($exif,true), FILE_APPEND);
		if (!$exif) return;
		$ort = $exif['Orientation'];
		switch ($ort) {
			case 1: // nothing
				break;
			case 2: // horizontal flip
				$flp = 1;
				break;
			case 3: // 180 rotate left
				$rot = 180;
				break;
			case 4: // vertical flip
				$flp = 2;
				break;
			case 5: // vertical flip + 90 rotate right
				$flp = 2;
				$rot = 90;
				break;
			case 6: // 90 rotate right
				$rot = 90;
				break;
			case 7: // horizontal flip + 90 rotate right
				$flp = 1;
				$rot = 90;
				break;
			case 8: // 90 rotate left
				$rot = -90;
				break;
		}
		if (($flp + $rot) !== 0) {
			try {
				if ($flp==1) { $this->imgk->flipImage(); }
				else if ($flp==2) { $this->imgk->flopImage(); }
				if ($rot!==0) { $this->imgk->rotateImage(new ImagickPixel('#00000000'), $rot); }
				$this->imgk->setImageOrientation(imagick::ORIENTATION_TOPLEFT);
				$this->imgk->writeImage(realpath($dest));
				return filesize(realpath($dest)) - $osize;
			}
			catch(Exception $e) {
			//	die('Error when orienting image: ' . $e->getMessage());
				$this->errs[] = 'Error when orienting image: ' . $e->getMessage();
			}
		}
		return 0;
	}

}