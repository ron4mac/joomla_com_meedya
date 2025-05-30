<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2015-2024 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.4.0
*/
defined('_JEXEC') or die;

use Joomla\CMS\Component\Router\RouterFactoryInterface;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\Extension\Service\Provider\CategoryFactory;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\CMS\HTML\Registry;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use J4xdemos\Component\Mywalks\Administrator\Extension\MywalksComponent;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class implements ServiceProviderInterface
{
	public function register(Container $container)
	{
		$container->registerServiceProvider(new MVCFactory('\\RJCreations\\Component\\Meedya'));
		$container->registerServiceProvider(new ComponentDispatcherFactory('\\RJCreations\\Component\\Meedya'));
		$container->set(
				ComponentInterface::class,
				function (Container $container)
				{
					$component = new MVCComponent($container->get(ComponentDispatcherFactoryInterface::class));
					$component->setMVCFactory($container->get(MVCFactoryInterface::class));
					return $component;
		}
		);
	}
};
