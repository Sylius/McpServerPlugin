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
    name: 'fetch_product',
    description: 'fetch_product(code) → raw JSON object of a Sylius product with inline images. Key field: variants[] (URLs like "…/product-variants/Basic_regular-variant-3"); pass the final path segment to fetch_product_variant for full variant details.',
)]
final readonly class Fetch
{
    use DownloadsImagesTrait;

    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    /**
     * @param string $code Unique product code, e.g. "Basic_regular".
     *
     * @return Content[]
     */
    public function __invoke(string $code): array
    {
        $json = $this->fetchProductJson($code);
        $imageUrls = $this->extractImageUrls(json_decode($json, true));

        return [new TextContent($json), ...$this->downloadImages($imageUrls)];
    }

    private function fetchProductJson(string $code): string
    {
        return $this->httpClient->request('GET', sprintf('products/%s', urlencode($code)))->getContent();
    }

    /**
     * @return string[]
     */
    private function extractImageUrls(mixed $productData): array
    {
        if (!\is_array($productData) || !\is_array($productData['images'] ?? null)) {
            return [];
        }

        $urls = [];
        foreach ($productData['images'] as $image) {
            if (\is_array($image) && isset($image['path']) && \is_string($image['path']) && $image['path'] !== '') {
                $urls[] = $image['path'];
            }
        }

        return $urls;
    }
}
