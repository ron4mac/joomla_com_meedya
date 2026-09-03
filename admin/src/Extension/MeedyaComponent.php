<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2026 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.4.3
*/
namespace RJCreations\Component\Meedya\Administrator\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use RJCreations\Component\Meedya\Administrator\Helper\Html\Mygrid;
use Psr\Container\ContainerInterface;
	
class MeedyaComponent extends MVCComponent implements BootableExtensionInterface
{
	use HTMLRegistryAwareTrait;

	public function boot(ContainerInterface $container): void
	{
		$this->getRegistry()->register('mygrid', new Mygrid());
	}

}
