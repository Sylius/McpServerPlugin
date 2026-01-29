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

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Sylius\McpServerPlugin\DependencyInjection\Configuration;

final class SyliusMcpServerConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /** @test */
    public function it_has_default_server_name(): void
    {
        $this->assertProcessedConfigurationEquals(
            [],
            ['server' => ['name' => 'Sylius MCP Server']],
            'server.name',
        );
    }

    /** @test */
    public function it_allows_to_define_server_name(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['server' => ['name' => 'Custom MCP Server']]],
            ['server' => ['name' => 'Custom MCP Server']],
            'server.name',
        );
    }

    /** @test */
    public function it_has_default_server_version(): void
    {
        $this->assertProcessedConfigurationEquals(
            [],
            ['server' => ['version' => '0.1.0']],
            'server.version',
        );
    }

    /** @test */
    public function it_allows_to_define_server_version(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['server' => ['version' => '1.0.0']]],
            ['server' => ['version' => '1.0.0']],
            'server.version',
        );
    }

    /** @test */
    public function it_has_default_transport_configuration(): void
    {
        $this->assertProcessedConfigurationEquals(
            [],
            [
                'server' => [
                    'transport' => [
                        'host' => '127.0.0.1',
                        'port' => 8080,
                        'prefix' => 'mcp',
                        'enable_json_response' => false,
                        'ssl' => [
                            'enabled' => false,
                            'context' => [],
                        ],
                    ],
                ],
            ],
            'server.transport',
        );
    }

    /** @test */
    public function it_allows_to_define_transport_configuration(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[
                'server' => [
                    'transport' => [
                        'host' => '127.9.9.9',
                        'port' => 9090,
                        'prefix' => 'custom',
                        'enable_json_response' => true,
                        'ssl' => [
                            'enabled' => true,
                            'context' => ['verify_peer' => false],
                        ],
                    ],
                ],
            ]],
            [
                'server' => [
                    'transport' => [
                        'host' => '127.9.9.9',
                        'port' => 9090,
                        'prefix' => 'custom',
                        'enable_json_response' => true,
                        'ssl' => [
                            'enabled' => true,
                            'context' => ['verify_peer' => false],
                        ],
                    ],
                ],
            ],
            'server.transport',
        );
    }

    /** @test */
    public function it_has_default_session_configuration(): void
    {
        $this->assertProcessedConfigurationEquals(
            [],
            [
                'server' => [
                    'session' => [
                        'driver' => 'cache',
                        'ttl' => 3600,
                    ],
                ],
            ],
            'server.session',
        );
    }

    /** @test */
    public function it_allows_to_define_session_configuration(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[
                'server' => [
                    'session' => [
                        'driver' => 'array',
                        'ttl' => 7200,
                    ],
                ],
            ]],
            [
                'server' => [
                    'session' => [
                        'driver' => 'array',
                        'ttl' => 7200,
                    ],
                ],
            ],
            'server.session',
        );
    }

    /** @test */
    public function it_has_default_discovery_locations(): void
    {
        $this->assertProcessedConfigurationEquals(
            [],
            [
                'server' => [
                    'discovery' => [
                        'locations' => [
                            [
                                'base_path' => '%sylius_mcp_server.plugin_root%',
                                'scan_dirs' => ['src/Tool'],
                            ],
                        ],
                    ],
                ],
            ],
            'server.discovery.locations',
        );
    }

    /** @test */
    public function it_allows_to_define_discovery_locations(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[
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
            ]],
            [
                'server' => [
                    'discovery' => [
                        'locations' => [
                            [
                                'base_path' => 'test/path',
                                'scan_dirs' => ['src/Tool', 'src/Resource'],
                            ],
                            [
                                'base_path' => '%sylius_mcp_server.plugin_root%',
                                'scan_dirs' => ['src/Tool'],
                            ],
                        ],
                    ],
                ],
            ],
            'server.discovery.locations',
        );
    }

    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }
}
