<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2016 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class ImageProcessor
{
	protected $errs = array();
	protected $truecolor = false;
	protected $src = null;
	protected $res = null;
	protected $filesize;
	protected $imgInfo;
	protected $width;
	protected $height;
	protected $string;

	public function __construct ($src)
	{
		if (function_exists('imagecreatetruecolor')) {
			$this->truecolor = true;
		}
		$this->src = realpath($src);
		$this->filesize = round(filesize($this->src) / 1000);
		$this->imgInfo = getimagesize($this->src);

		$this->width = $this->imgInfo[0];
		$this->height = $this->imgInfo[1];
		$this->string = $this->imgInfo[3];

		switch ($this->imgInfo[0]) {
			case 1:
				$this->res = imagecreatefromgif($this->src);
				break;
			case 2:
				$this->res = imagecreatefromjpeg($this->src);
				break;
			case 3:
				$this->res = imagecreatefrompng($this->src);
				break;
		}

		if (!$this->res) throw new Exception('INVALID IMAGE');
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
