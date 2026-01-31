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
    name: 'select_shipping_method',
    description: 'select_shipping_method(tokenValue, shipmentId, shippingMethodCode) → raw JSON object of the order after choosing a shipping method. Amounts in the response remain integers in minor units (÷100).',
)]
final readonly class ShippingMethodSelect
{
    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    /**
     * @param string $tokenValue          Cart token (from create_order / fetch_order).
     * @param int    $shipmentId          Shipment ID found in order.shipments[].id.
     * @param string $shippingMethodCode  Code returned by list_shipping_methods(), e.g. "fedex".
     */
    public function __invoke(
        string $tokenValue,
        int $shipmentId,
        string $shippingMethodCode,
    ): string {
        $response = $this->httpClient->request(
            'PATCH',
            sprintf('orders/%s/shipments/%d', urlencode($tokenValue), $shipmentId),
            [
                'json' => ['shippingMethod' => $shippingMethodCode],
            ],
        );

        return $response->getContent();
    }
}
