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
use Mcp\Schema\Content\Content;
use Mcp\Schema\Content\TextContent;
use Sylius\McpServerPlugin\Tool\DownloadsImagesTrait;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[McpTool(
    name: 'search_products',
    description: 'search_products(query, page?, itemsPerPage?) → raw JSON Hydra collection of Sylius products with inline images (main image per product). Each product exposes variants[] as URLs like "…/product-variants/Basic_regular-variant-3"; pass the final path segment (variant code) to fetch_product_variant for full variant details.',
)]
final readonly class Search
{
    use DownloadsImagesTrait;

    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    /**
     * @param string $query        Keyword fragment to match against translations.name.
     * @param int    $page         Page number (1-based). Default = 1.
     * @param int    $itemsPerPage Items per page. Default = 30.
     *
     * @return Content[]
     */
    public function __invoke(string $query, int $page = 1, int $itemsPerPage = 30): array
    {
        $json = $this->fetchProductsJson($query, $page, $itemsPerPage);
        $products = $this->decodeProductMembers($json);
        $imageUrls = $this->collectMainImageUrlsFromProducts($products);

        return [new TextContent($json), ...$this->downloadImages($imageUrls)];
    }

    private function fetchProductsJson(string $query, int $page, int $itemsPerPage): string
    {
        return $this->httpClient->request('GET', 'products', [
            'query' => [
                'translations.name' => $query,
                'page' => $page,
                'itemsPerPage' => $itemsPerPage,
            ],
        ])->getContent();
    }

    /**
     * @return mixed[]
     */
    private function decodeProductMembers(string $json): array
    {
        $data = json_decode($json, true);

        return \is_array($data) && \is_array($data['hydra:member'] ?? null) ? $data['hydra:member'] : [];
    }

    /**
     * @param mixed[] $products
     *
     * @return string[]
     */
    private function collectMainImageUrlsFromProducts(array $products): array
    {
        $urls = [];
        foreach ($products as $product) {
            $images = \is_array($product) && \is_array($product['images'] ?? null) ? $product['images'] : [];
            $url = $this->findMainImageUrl($images);
            if ($url !== null) {
                $urls[] = $url;
            }
        }

        return $urls;
    }

    /**
     * @param mixed[] $images
     */
    private function findMainImageUrl(array $images): ?string
    {
        $firstUrl = null;

        foreach ($images as $image) {
            if (!\is_array($image) || !isset($image['path']) || !\is_string($image['path']) || $image['path'] === '') {
                continue;
            }

            $firstUrl ??= $image['path'];

            if (($image['type'] ?? null) === 'main') {
                return $image['path'];
            }
        }

        return $firstUrl;
    }
}
