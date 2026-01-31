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
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[McpTool(
    name: 'search_products',
    description: 'search_products(query, page?, itemsPerPage?) → raw JSON Hydra collection of Sylius products. Each product exposes variants[] as URLs like “…/product-variants/Basic_regular-variant-3”; pass the final path segment (variant code) to fetch_product_variant for full variant details.',
)]
final readonly class Search
{
    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    /**
     * @param string $query         Keyword fragment to match against translations.name.
     * @param int    $page          Page number (1-based). Default = 1.
     * @param int    $itemsPerPage  Items per page. Default = 30.
     */
    public function __invoke(string $query, int $page = 1, int $itemsPerPage = 30): string
    {
        $response = $this->httpClient->request(
            'GET',
            'products',
            [
                'query' => [
                    'translations.name' => $query,
                    'page' => $page,
                    'itemsPerPage' => $itemsPerPage,
                ],
            ],
        );

        return $response->getContent();
    }
}
