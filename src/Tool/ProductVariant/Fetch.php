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

namespace Sylius\McpServerPlugin\Tool\ProductVariant;

use Mcp\Capability\Attribute\McpTool;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[McpTool(
    name: 'fetch_product_variant',
    description: 'fetch_product_variant(variantCode) → raw JSON object of a Sylius product variant. Prices (price, originalPrice, lowestPriceBeforeDiscount) are integers in minor units (÷100) and have no currency code.',
)]
final readonly class Fetch
{
    public function __construct(
        #[Autowire(service: 'sylius_mcp_server.http_client.api_shop')]
        private HttpClientInterface $httpClient,
    ) {
    }

    /**
     * @param string $variantCode Variant code, e.g. "Basic_winter_hot_cap-variant-0"
     */
    public function __invoke(string $variantCode): string
    {
        $response = $this->httpClient->request(
            'GET',
            sprintf('product-variants/%s', urlencode($variantCode)),
        );

        return $response->getContent();
    }
}
