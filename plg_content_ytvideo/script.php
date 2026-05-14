<?php

/*
 * @package     Joomla.Plugin
 * @subpackage  Content.ytvideo
 * @copyright   Copyright (C) Aleksey A. Morozov. All rights reserved.
 * @copyright   Copyright (C) 2026 Vitaliy Magnum (https://www.magnumblog.space). Joomla 6 migration.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class implements ServiceProviderInterface {
    public function register(Container $container): void
    {
        $container->set(
            InstallerScriptInterface::class,
            new class ($container->get(DatabaseInterface::class)) implements InstallerScriptInterface {
                public function __construct(private readonly DatabaseInterface $db) {}

                public function install(InstallerAdapter $adapter): bool
                {
                    $this->enablePlugin();
                    return true;
                }

                public function update(InstallerAdapter $adapter): bool
                {
                    $this->enablePlugin();
                    return true;
                }

                public function uninstall(InstallerAdapter $adapter): bool
                {
                    return true;
                }

                public function preflight(string $type, InstallerAdapter $adapter): bool
                {
                    return true;
                }

                public function postflight(string $type, InstallerAdapter $adapter): bool
                {
                    if (\in_array($type, ['install', 'update'])) {
                        $this->enablePlugin();
                    }
                    return true;
                }

                private function enablePlugin(): void
                {
                    $query = $this->db->createQuery()
                        ->update($this->db->quoteName('#__extensions'))
                        ->set($this->db->quoteName('enabled') . ' = 1')
                        ->where($this->db->quoteName('type') . ' = ' . $this->db->quote('plugin'))
                        ->where($this->db->quoteName('element') . ' = ' . $this->db->quote('ytvideo'))
                        ->where($this->db->quoteName('folder') . ' = ' . $this->db->quote('content'));
                    $this->db->setQuery($query)->execute();
                }
            }
        );
    }
};
