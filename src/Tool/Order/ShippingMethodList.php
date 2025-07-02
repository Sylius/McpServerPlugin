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

use PhpMcp\Server\Attributes\McpTool;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[McpTool(
    name: 'list_shipping_methods',
    description: 'list_shipping_methods(tokenValue, shipmentId, page?, itemsPerPage?) → raw JSON Hydra collection of available shipping methods for the given shipment. Each member has code, name and price in minor units (÷100); no currency code is included.',
)]
final readonly class ShippingMethodList
{
    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    /**
     * @param string $tokenValue  Cart token (from create_order / fetch_order).
     * @param int    $shipmentId  Numeric shipment ID returned in the order JSON.
     * @param int    $page        Page number (1-based), default 1.
     * @param int    $itemsPerPage Items per page, default 30.
     */
    public function __invoke(
        string $tokenValue,
        int $shipmentId,
        int $page = 1,
        int $itemsPerPage = 30,
    ): string {
        $response = $this->httpClient->request(
            'GET',
            sprintf(
                'orders/%s/shipments/%d/methods',
                urlencode($tokenValue),
                $shipmentId,
            ),
            ['query' => [
                'page' => $page,
                'itemsPerPage' => $itemsPerPage,
            ]],
        );

        return $response->getContent();
    }
}
