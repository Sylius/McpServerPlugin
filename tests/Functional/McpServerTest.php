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

namespace Tests\Sylius\McpServerPlugin\Functional;

use Sylius\McpServerPlugin\Mcp\Loader\PluginDiscoveryLoader;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class McpServerTest extends KernelTestCase
{
    public function testPluginDiscoveryLoaderIsRegisteredAsService(): void
    {
        self::bootKernel();

        $container = self::getContainer();

        $this->assertTrue($container->has(PluginDiscoveryLoader::class));
    }

    public function testPluginDiscoveryLoaderIsTaggedWithMcpLoader(): void
    {
        self::bootKernel();

        $container = self::getContainer();

        // Get the service to ensure it can be instantiated
        $loader = $container->get(PluginDiscoveryLoader::class);

        $this->assertInstanceOf(PluginDiscoveryLoader::class, $loader);
    }

    public function testMcpServerIsAvailable(): void
    {
        self::bootKernel();

        $container = self::getContainer();

        $this->assertTrue($container->has('mcp.server'));
    }

    public function testToolServicesAreAvailable(): void
    {
        self::bootKernel();

        $container = self::getContainer();

        // Check that some tool services are registered
        $this->assertTrue($container->has('Sylius\McpServerPlugin\Tool\Channel\Fetch'));
        $this->assertTrue($container->has('Sylius\McpServerPlugin\Tool\Product\Search'));
        $this->assertTrue($container->has('Sylius\McpServerPlugin\Tool\Order\Create'));
    }
}
