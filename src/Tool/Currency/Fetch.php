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

namespace Sylius\McpServerPlugin\Tool\Currency;

use Mcp\Capability\Attribute\McpTool;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[McpTool(
    name: 'fetch_currency',
    description: 'fetch_currency(code) → raw JSON object describing the Sylius currency identified by the given ISO code; response contains fields "code" and "name".',
)]
final readonly class Fetch
{
    public function __construct(
        #[Autowire(service: 'sylius_mcp_server.http_client.api_shop')]
        private HttpClientInterface $httpClient,
    ) {
    }

    /**
     * @param string $code ISO-4217 currency code, e.g. "EUR"
     */
    public function __invoke(string $code): string
    {
        $response = $this->httpClient->request(
            'GET',
            sprintf('currencies/%s', $code),
        );

        return $response->getContent();
    }
}
