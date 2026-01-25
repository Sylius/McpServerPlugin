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

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class McpServerHttpTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

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
        $sessionId = $this->initializeSession();

        $response = $this->sendMcpRequest([
            'jsonrpc' => '2.0',
            'id' => 2,
            'method' => 'tools/list',
            'params' => [],
        ], $sessionId);

        $this->assertSame(2, $response['id']);
        $this->assertArrayHasKey('result', $response);
        $this->assertArrayHasKey('tools', $response['result']);

        $tools = $response['result']['tools'];
        $toolNames = array_column($tools, 'name');

        $this->assertContains('fetch_channel', $toolNames);
        $this->assertContains('search_products', $toolNames);
        $this->assertContains('create_order', $toolNames);
        $this->assertGreaterThanOrEqual(15, count($tools));
    }

    public function testToolsHaveCorrectSchema(): void
    {
        $sessionId = $this->initializeSession();

        $response = $this->sendMcpRequest([
            'jsonrpc' => '2.0',
            'id' => 2,
            'method' => 'tools/list',
            'params' => [],
        ], $sessionId);

        $tools = $response['result']['tools'];
        $toolsByName = array_column($tools, null, 'name');

        $searchProducts = $toolsByName['search_products'];
        $this->assertSame('object', $searchProducts['inputSchema']['type']);
        $this->assertArrayHasKey('query', $searchProducts['inputSchema']['properties']);
        $this->assertContains('query', $searchProducts['inputSchema']['required']);

        $fetchChannel = $toolsByName['fetch_channel'];
        $this->assertSame('object', $fetchChannel['inputSchema']['type']);
    }

    private function initializeSession(): string
    {
        $this->client->request(
            'POST',
            '/_mcp',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            (string) json_encode([
                'jsonrpc' => '2.0',
                'id' => 1,
                'method' => 'initialize',
                'params' => [
                    'protocolVersion' => '2024-11-05',
                    'capabilities' => [],
                    'clientInfo' => ['name' => 'test', 'version' => '1.0'],
                ],
            ]),
        );

        $response = $this->client->getResponse();

        return $response->headers->get('Mcp-Session-Id', '');
    }

    /**
     * @param array<string, mixed> $request
     *
     * @return array<string, mixed>
     */
    private function sendMcpRequest(array $request, ?string $sessionId = null): array
    {
        $headers = ['CONTENT_TYPE' => 'application/json'];
        if ($sessionId !== null) {
            $headers['HTTP_MCP_SESSION_ID'] = $sessionId;
        }

        $this->client->request(
            'POST',
            '/_mcp',
            [],
            [],
            $headers,
            (string) json_encode($request),
        );

        $response = $this->client->getResponse();
        $content = $response->getContent();

        return json_decode($content !== false ? $content : '{}', true);
    }
}
