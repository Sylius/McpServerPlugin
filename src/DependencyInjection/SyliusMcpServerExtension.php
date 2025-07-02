<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\McpServerPlugin\DependencyInjection;

use Sylius\Bundle\CoreBundle\DependencyInjection\PrependDoctrineMigrationsTrait;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SyliusMcpServerExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    use PrependDoctrineMigrationsTrait;

    /** @param array<string, mixed> $configs */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);

        $container->setParameter('sylius_mcp_server.plugin_root', dirname(__DIR__, 2));

        $container->setParameter('sylius_mcp_server.server.name', $config['server']['name']);
        $container->setParameter('sylius_mcp_server.server.version', $config['server']['version']);
        $container->setParameter('sylius_mcp_server.server.transport.host', $config['server']['transport']['host']);
        $container->setParameter('sylius_mcp_server.server.transport.port', $config['server']['transport']['port']);
        $container->setParameter('sylius_mcp_server.server.transport.prefix', $config['server']['transport']['prefix']);
        $container->setParameter('sylius_mcp_server.server.transport.enable_json_response', $config['server']['transport']['enable_json_response']);
        $container->setParameter('sylius_mcp_server.server.transport.ssl.enabled', $config['server']['transport']['ssl']['enabled']);
        $container->setParameter('sylius_mcp_server.server.transport.ssl.context', $config['server']['transport']['ssl']['context']);
        $container->setParameter('sylius_mcp_server.server.session.driver', $config['server']['session']['driver']);
        $container->setParameter('sylius_mcp_server.server.session.ttl', $config['server']['session']['ttl']);
        $container->setParameter('sylius_mcp_server.server.discovery.locations', $config['server']['discovery']['locations']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.xml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->prependDoctrineMigrations($container);
    }

    protected function getMigrationsNamespace(): string
    {
        return 'Sylius\McpServerPlugin\Migrations';
    }

    protected function getMigrationsDirectory(): string
    {
        return '@SyliusMcpServerPlugin/src/Migrations';
    }

    protected function getNamespacesOfMigrationsExecutedBefore(): array
    {
        return [
            'Sylius\Bundle\CoreBundle\Migrations',
        ];
    }
}
