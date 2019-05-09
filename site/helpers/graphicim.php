<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2019 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

include_once 'imgproc.php';

class ImageProcessor extends ImageProc
{
	protected $errs = array();

	public function __construct ($src)
	{
		parent::__construct($src);

		if (RJC_DBUG) { MeedyaHelper::log('ImagemagickProc'); }
	}

	public function getErrors ()
	{
		return $this->errs;
	}

	public function createThumb ($dest, $ext, $maxW=120, $maxH=120, $sqr=true)
	{
		if (RJC_DBUG) { MeedyaHelper::log(print_r(array('createThumb',$this->src,getimagesize($this->src)),true)); }
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
		// convert -define jpeg:size=200x200 {src} -thumbnail 100x100^ -gravity center -extent 100x100 {dest}
	//	$cmd = "convert {$this->src} -thumbnail {$w}x{$h} -quality 90 {$dfil}  2>&1";
		$_s = escapeshellarg($this->src);
		$_d = escapeshellarg($dfil);
		$cmd = "convert {$_s} -thumbnail {$maxW}x{$maxH}^ -gravity center -extent 120x120 -sharpen 0x1 -quality 90 {$_d}  2>&1";
		if (RJC_DBUG) { MeedyaHelper::log($cmd); }
		exec($cmd, $output, $retval);
		if (RJC_DBUG) { MeedyaHelper::log(print_r(array($output, $retval),true)); }
		return filesize($dfil);
	}

	public function createMedium ($dest, $ext, $maxW=1200, $maxH=0)
	{
		if (RJC_DBUG) { MeedyaHelper::log(print_r(array('createMedium',$this->src,getimagesize($this->src)),true)); }
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
		$cmd = "convert {$_s} -resize {$w}x{$h}\> -quality 90 {$_d}  2>&1";
		if (RJC_DBUG) { MeedyaHelper::log($cmd); }
		exec($cmd, $output, $retval);
		if (RJC_DBUG) { MeedyaHelper::log(print_r(array($output, $retval), true)); }
		return filesize($dfil);
	}

	public function orientImage ($dest)
	{
		$_s = escapeshellarg($this->src);
		$_d = escapeshellarg($dest);
	    $cmd = "convert {$_s} -auto-orient {$_d}  2>&1";
        exec($cmd, $output, $retval);
		if (RJC_DBUG) { MeedyaHelper::log(print_r(array($output, $retval), true)); }
		$this->src = $dest;
		return filesize($this->src);
	}

}
