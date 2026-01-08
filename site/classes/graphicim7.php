<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2025 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.4.2
*/
defined('_JEXEC') or die;

use RJCreations\Component\Meedya\Site\Helper\MeedyaHelper;

include_once 'imgproc.php';

class ImageProcessor extends ImageProc
{
	public $ipp = 'IM7';
	protected $errs = [];

	public function __construct ($src)
	{
		parent::__construct($src);

		if (RJC_DBUG) { MeedyaHelper::log('Imagemagick7Proc'); }
	}


	public function getErrors ()
	{
		return $this->errs;
	}


	public function createThumb ($dest, $ext, $maxW=120, $maxH=120, $sqr=true)
	{
		if (RJC_DBUG) { MeedyaHelper::log(print_r(['createThumb',$this->src,getimagesize($this->src)],true)); }
		if (!isset($this->img_width)) return 0;
		$dfil = $dest.$ext;
		$w = $this->img_width;
		$h = $this->img_height;
		if ($maxH) {
			$r = $w/$h;
			$h = $maxH;
			$w = $r * $maxH;
		} else {
			$r = $h/$w;
			$w = $maxW;
			$h = round($r * $maxW);
		}
		if (RJC_DBUG) { MeedyaHelper::log($dfil.':'.$w.':'.$h); }
		$_s = escapeshellarg($this->src);
		$_d = escapeshellarg($dfil);
		$cmd = "magick {$_s} -thumbnail {$maxW}x{$maxH}^ -gravity center -extent 120x120 -sharpen 0x1 -quality 90 {$_d}  2>&1";
		if (RJC_DBUG) { MeedyaHelper::log($cmd); }
		exec($cmd, $output, $retval);
		if (RJC_DBUG) { MeedyaHelper::log(print_r([$output, $retval],true)); }
		return filesize($dfil);
	}


	public function createMedium ($dest, $ext, $maxW=1200, $maxH=0)
	{
		if (RJC_DBUG) { MeedyaHelper::log(print_r(['createMedium',$this->src,getimagesize($this->src)],true)); }
		if (!isset($this->img_width)) return 0;
		$dfil = $dest.$ext;
		$w = $this->img_width;
		$h = $this->img_height;
		if ($maxW && $maxH) {
			list($w,$h) = $this->fitInRect($this->img_width, $this->img_height, $maxW, $maxH);
		} elseif ($maxH) {
			$r = $w/$h;
			$h = $maxH;
			$w = $r * $maxH;
		} else {
			$r = $h/$w;
			$w = $maxW;
			$h = round($r * $maxW);
		}
		if (RJC_DBUG) { MeedyaHelper::log($dfil.':'.$w.':'.$h); }
		$_s = escapeshellarg($this->src);
		$_d = escapeshellarg($dfil);
		$cmd = "magick {$_s} -resize {$w}x{$h}\> -quality 90 {$_d}  2>&1";
		if (RJC_DBUG) { MeedyaHelper::log($cmd); }
		exec($cmd, $output, $retval);
		if (RJC_DBUG) { MeedyaHelper::log(print_r([$output, $retval], true)); }
		return filesize($dfil);
	}


	public function orientImage ($dest)
	{
		$_s = escapeshellarg($this->src);
		$_d = escapeshellarg($dest);
	    $cmd = "magick {$_s} -auto-orient {$_d}  2>&1";
        exec($cmd, $output, $retval);
		if (RJC_DBUG) { MeedyaHelper::log(print_r([$output, $retval], true)); }
		$this->src = $dest;
		parent::refresh();
		return filesize($this->src);
	}

}
