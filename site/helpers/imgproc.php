<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2019 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

class ImageProc
{
	protected $src = null;
	protected $img_width;
	protected $img_height;
	protected $img_type;

	/*
	needed actions to correctly orient an image based on its current orientation
	array(<rotate angle>, <mirror>)
	actions: rotate, flip ⬍ , flop ⬌

	  1        2       3      4         5            6           7          8

	888888  888888      88  88      8888888888  88                  88  8888888888
	88          88      88  88      88  88      88  88          88  88      88  88
	8888      8888    8888  8888    88          8888888888  8888888888          88
	88          88      88  88
	88          88  888888  888888
	*/

	protected $orientAction = array(
		1 => array(0, false),		// <none>
		2 => array(0, true),		// flop
		3 => array(180, false),		// rotate(180) or flip,flop
		4 => array(180, true),		// flip
		5 => array(-90, true),		// rotate(-90), flop
		6 => array(-90, false),		// rotate(-90)
		7 => array(90, true),		// rotate(90), flip
		8 => array(90, false)		// rotate(90)
	);

	public function __construct ($src)
	{
		$this->src = $src;
		list($this->img_width, $this->img_height, $this->img_type) = getimagesize($src);
	}

}
