<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2024 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.4.0
*/
namespace RJCreations\Component\Meedya\Site\View\Manage;
die('ZXZXZXZZXZXZXXZ');
defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class RawView extends BaseHtmlView
{

	public function __construct ($config = [])
	{
	//	if (RJC_DBUG) { MeedyaHelper::log('MeedyaViewManageRaw'); }
		parent::__construct($config);
	}

	public function display ($tpl=null)
	{
	//	echo $this->get('Content');
	echo 'tmpl=component& tmpl=component& tmpl=component& tmpl=component&';
	}

}
