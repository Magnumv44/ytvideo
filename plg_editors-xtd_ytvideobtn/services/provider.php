<?php

/*
 * @package     Joomla.Plugin
 * @subpackage  Editors-xtd.ytvideobtn
 * @copyright   Copyright (C) Aleksey A. Morozov. All rights reserved.
 * @copyright   Copyright (C) 2026 Vitaliy Magnum (https://www.magnumblog.space). Joomla 6 migration.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\Plugin\EditorsXtd\Ytvideobtn\Extension\Ytvideobtn;

return new class implements ServiceProviderInterface {
    public function register(Container $container): void
    {
        $container->set(
            PluginInterface::class,
            static function (Container $container) {
                $plugin = new Ytvideobtn(
                    $container->get(DispatcherInterface::class),
                    (array) PluginHelper::getPlugin('editors-xtd', 'ytvideobtn')
                );
                $plugin->setApplication(Factory::getApplication());

                return $plugin;
            }
        );
    }
};
