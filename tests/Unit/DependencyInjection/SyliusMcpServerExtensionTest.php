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

namespace Tests\Sylius\McpServerPlugin\Unit\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\McpServerPlugin\DependencyInjection\SyliusMcpServerExtension;

final class SyliusMcpServerExtensionTest extends AbstractExtensionTestCase
{
    /** @test */
    public function it_loads_plugin_root_parameter(): void
    {
        $this->load();

        $this->assertContainerBuilderHasParameter(
            'sylius_mcp_server.plugin_root',
            dirname(__DIR__, 3),
        );
    }

    /** @test */
    public function it_loads_server_name_parameter(): void
    {
        $this->load([
            'server' => [
                'name' => 'Test MCP Server',
            ],
        ]);

        $this->assertContainerBuilderHasParameter(
            'sylius_mcp_server.server.name',
            'Test MCP Server',
        );
    }

    /** @test */
    public function it_loads_server_version_parameter(): void
    {
        $this->load([
            'server' => [
                'version' => '1.0.0',
            ],
        ]);

        $this->assertContainerBuilderHasParameter(
            'sylius_mcp_server.server.version',
            '1.0.0',
        );
    }

    /** @test */
    public function it_loads_server_transport_parameters(): void
    {
        $this->load([
            'server' => [
                'transport' => [
                    'host' => '127.127.0.1',
                    'port' => 9090,
                    'prefix' => 'mcp',
                    'enable_json_response' => true,
                    'ssl' => [
                        'enabled' => true,
                        'context' => ['verify_peer' => false],
                    ],
                ],
            ],
        ]);

        $this->assertContainerBuilderHasParameter(
            'sylius_mcp_server.server.transport.host',
            '127.127.0.1',
        );
        $this->assertContainerBuilderHasParameter(
            'sylius_mcp_server.server.transport.port',
            9090,
        );
        $this->assertContainerBuilderHasParameter(
            'sylius_mcp_server.server.transport.prefix',
            'mcp',
        );
        $this->assertContainerBuilderHasParameter(
            'sylius_mcp_server.server.transport.enable_json_response',
            true,
        );
        $this->assertContainerBuilderHasParameter(
            'sylius_mcp_server.server.transport.ssl.enabled',
            true,
        );
        $this->assertContainerBuilderHasParameter(
            'sylius_mcp_server.server.transport.ssl.context',
            ['verify_peer' => false],
        );
    }

    /** @test */
    public function it_loads_server_session_parameters(): void
    {
        $this->load([
            'server' => [
                'session' => [
                    'driver' => 'array',
                    'ttl' => 7200,
                ],
            ],
        ]);

        $this->assertContainerBuilderHasParameter(
            'sylius_mcp_server.server.session.driver',
            'array',
        );
        $this->assertContainerBuilderHasParameter(
            'sylius_mcp_server.server.session.ttl',
            7200,
        );
    }

    /** @test */
    public function it_loads_server_discovery_locations(): void
    {
        $this->load([
            'server' => [
                'discovery' => [
                    'locations' => [
                        [
                            'base_path' => 'test/path',
                            'scan_dirs' => ['src/Tool', 'src/Resource'],
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertContainerBuilderHasParameter(
            'sylius_mcp_server.server.discovery.locations',
            [
                [
                    'base_path' => 'test/path',
                    'scan_dirs' => ['src/Tool', 'src/Resource'],
                ],
                [
                    'base_path' => '%sylius_mcp_server.plugin_root%',
                    'scan_dirs' => ['src/Tool'],
                ],
            ],
        );
    }

    protected function getContainerExtensions(): array
    {
        return [new SyliusMcpServerExtension()];
    }
}
