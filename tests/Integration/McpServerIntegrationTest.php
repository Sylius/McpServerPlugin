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

namespace Tests\Sylius\McpServerPlugin\Integration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

final class McpServerIntegrationTest extends TestCase
{
    public function testInitializeReturnsServerInfo(): void
    {
        $response = $this->sendMcpRequest([
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'initialize',
            'params' => [
                'protocolVersion' => '2024-11-05',
                'capabilities' => [],
                'clientInfo' => ['name' => 'test', 'version' => '1.0'],
            ],
        ]);

        $this->assertSame('2.0', $response['jsonrpc']);
        $this->assertSame(1, $response['id']);
        $this->assertArrayHasKey('result', $response);
        $this->assertSame('Sylius MCP Server', $response['result']['serverInfo']['name']);
    }

    public function testToolsListReturnsAllTools(): void
    {
        $responses = $this->sendMcpRequests([
            [
                'jsonrpc' => '2.0',
                'id' => 1,
                'method' => 'initialize',
                'params' => [
                    'protocolVersion' => '2024-11-05',
                    'capabilities' => [],
                    'clientInfo' => ['name' => 'test', 'version' => '1.0'],
                ],
            ],
            [
                'jsonrpc' => '2.0',
                'id' => 2,
                'method' => 'tools/list',
                'params' => [],
            ],
        ]);

        $toolsResponse = $responses[1];

        $this->assertSame(2, $toolsResponse['id']);
        $this->assertArrayHasKey('result', $toolsResponse);
        $this->assertArrayHasKey('tools', $toolsResponse['result']);

        $tools = $toolsResponse['result']['tools'];
        $toolNames = array_column($tools, 'name');

        $this->assertContains('fetch_channel', $toolNames);
        $this->assertContains('search_products', $toolNames);
        $this->assertContains('create_order', $toolNames);
        $this->assertGreaterThanOrEqual(15, count($tools));
    }

    public function testToolsHaveCorrectSchema(): void
    {
        $responses = $this->sendMcpRequests([
            [
                'jsonrpc' => '2.0',
                'id' => 1,
                'method' => 'initialize',
                'params' => [
                    'protocolVersion' => '2024-11-05',
                    'capabilities' => [],
                    'clientInfo' => ['name' => 'test', 'version' => '1.0'],
                ],
            ],
            [
                'jsonrpc' => '2.0',
                'id' => 2,
                'method' => 'tools/list',
                'params' => [],
            ],
        ]);

        $tools = $responses[1]['result']['tools'];
        $toolsByName = array_column($tools, null, 'name');

        // Check search_products has required query parameter
        $searchProducts = $toolsByName['search_products'];
        $this->assertSame('object', $searchProducts['inputSchema']['type']);
        $this->assertArrayHasKey('query', $searchProducts['inputSchema']['properties']);
        $this->assertContains('query', $searchProducts['inputSchema']['required']);

        // Check fetch_channel has no required parameters
        $fetchChannel = $toolsByName['fetch_channel'];
        $this->assertSame('object', $fetchChannel['inputSchema']['type']);
    }

    /**
     * @param array<string, mixed> $request
     * @return array<string, mixed>
     */
    private function sendMcpRequest(array $request): array
    {
        return $this->sendMcpRequests([$request])[0];
    }

    /**
     * @param array<int, array<string, mixed>> $requests
     * @return array<int, array<string, mixed>>
     */
    private function sendMcpRequests(array $requests): array
    {
        $input = implode("\n", array_map('json_encode', $requests));

        $process = new Process([
            'php',
            'vendor/bin/console',
            'mcp:server',
        ]);
        $process->setInput($input);
        $process->setTimeout(30);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->fail('MCP server process failed: ' . $process->getErrorOutput());
        }

        $output = trim($process->getOutput());
        $lines = explode("\n", $output);

        return array_map(
            fn (string $line) => json_decode($line, true),
            $lines
        );
    }
}