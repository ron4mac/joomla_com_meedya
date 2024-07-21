<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2024 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.4.0
*/

namespace RJCreations\Component\Meedya\Site\View\Startup;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
	protected $user;
	protected $params;
	protected $userPerms = null;
	protected $storQuota;
	protected $maxUpload;

	function display ($tpl=null)
	{
		$this->user = Factory::getUser();
		$this->params = Factory::getApplication()->getParams();
		$this->userPerms = \MeedyaHelper::getUserPermissions($this->user, $this->params);
		$this->storQuota = \MeedyaHelper::formatBytes(\MeedyaHelper::getResolvedOption('storQuota', 268435456));
		$this->maxUpload = \MeedyaHelper::formatBytes(\MeedyaHelper::getResolvedOption('maxUpload', 4194304));
		parent::display($tpl);
	}

}
