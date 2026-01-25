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

namespace Sylius\McpServerPlugin\Tool\Product;

use Mcp\Capability\Attribute\McpTool;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[McpTool(
    name: 'fetch_product',
    description: 'fetch_product(code) → raw JSON object of a Sylius product. Key field: variants[] (URLs like “…/product-variants/Basic_regular-variant-3”); pass the final path segment to fetch_product_variant for full variant details.',
)]
final readonly class Fetch
{
    public function __construct(
        #[Autowire(service: 'sylius_mcp_server.http_client.api_shop')]
        private HttpClientInterface $httpClient,
    ) {
    }

    /**
     * @param string $code Unique product code, e.g. "Basic_regular".
     */
    public function __invoke(string $code): string
    {
        $response = $this->httpClient->request(
            'GET',
            sprintf('products/%s', urlencode($code)),
        );

        return $response->getContent();
    }
}
