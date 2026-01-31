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
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[McpTool(
    name: 'search_product_variants',
    description: 'search_product_variants(productCodes[], page?, itemsPerPage?) → raw JSON Hydra collection of Sylius variants. Prices (price, originalPrice, lowestPriceBeforeDiscount) are integers in minor units (÷100) and lack a currency code.',
)]
final readonly class Search
{
    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    /**
     * @param string[] $productCodes 0-many product codes to filter by (e.g. ["Basic_regular"]).
     *                               Each will be expanded to /api/v2/shop/products/{code}.
     * @param int      $page         Page number (1-based). Default 1.
     * @param int      $itemsPerPage Items per page. Default 30.
     */
    public function __invoke(array $productCodes = [], int $page = 1, int $itemsPerPage = 30): string
    {
        $query = [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ];

        if ($productCodes !== []) {
            $query['product'] = array_map(
                static fn (string $code): string => sprintf('/api/v2/shop/products/%s', $code),
                $productCodes,
            );
        }

        $response = $this->httpClient->request(
            'GET',
            'product-variants',
            ['query' => $query],
        );

        return $response->getContent();
    }
}
