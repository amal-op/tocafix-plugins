<?php declare(strict_types=1);

namespace TocafixCustomPlugin;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use TocafixCustomPlugin\Setup\Installer;

class TocafixCustomPlugin extends Plugin
{
    public function install(InstallContext $installContext): void
    {
        $installer = new Installer(
            $this->container->get(Connection::class),
            $this->container
        );
        
        $installer->install($installContext);
    }

    public function update(UpdateContext $updateContext): void
    {
        $installer = new Installer(
            $this->container->get(Connection::class),
            $this->container
        );
        
        $installer->install($updateContext);
    }
}