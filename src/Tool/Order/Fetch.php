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
    name: 'fetch_order',
    description: 'fetch_order(tokenValue) → raw JSON object of a Sylius order (cart). Response includes checkout/payment/shipping states, currencyCode, items[], totals in minor units, and the same tokenValue.',
)]
final readonly class Fetch
{
    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    /**
     * @param string $tokenValue Order/cart token returned by create_order()
     */
    public function __invoke(string $tokenValue): string
    {
        $response = $this->httpClient->request(
            'GET',
            sprintf('orders/%s', urlencode($tokenValue)),
        );

        return $response->getContent();
    }
}
