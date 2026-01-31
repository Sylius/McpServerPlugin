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

namespace Sylius\McpServerPlugin\Tool\Order;

use Mcp\Capability\Attribute\McpTool;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[McpTool(
    name: 'add_item_to_order',
    description: 'add_item_to_order(tokenValue, productVariantIRI, quantity?) → raw JSON object of the updated Sylius order. Prices in the response are integers in minor units (÷100).',
)]
final readonly class AddItem
{
    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    /**
     * @param string $tokenValue      Order token received from create_order / fetch_order.
     * @param string $productVariant  **Full IRI** of the variant, e.g.
     *                                "/api/v2/shop/product-variants/Basic_regular-variant-0".
     *                                (Use variants[] returned by search_products or
     *                                product-variant tools; do **not** pass just the code.)
     * @param int    $quantity        Units to add; default 1.
     */
    public function __invoke(string $tokenValue, string $productVariant, int $quantity = 1): string
    {
        $response = $this->httpClient->request(
            'POST',
            sprintf('orders/%s/items', urlencode($tokenValue)),
            [
                'json' => [
                    'productVariant' => $productVariant,
                    'quantity' => $quantity,
                ],
            ],
        );

        return $response->getContent();
    }
}
