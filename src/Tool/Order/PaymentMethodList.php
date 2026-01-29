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
    name: 'list_payment_methods',
    description: 'list_payment_methods(tokenValue, paymentId, page?, itemsPerPage?) → raw JSON Hydra collection of available payment methods. Each member has code and name (e.g. “PAYPAL”, “Bank transfer”).',
)]
final readonly class PaymentMethodList
{
    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    /**
     * @param string $tokenValue Cart token (from create_order / fetch_order).
     * @param int    $paymentId  Payment ID found in order.payments[].id.
     * @param int    $page       Page number, default 1.
     * @param int    $itemsPerPage Items per page, default 30.
     */
    public function __invoke(
        string $tokenValue,
        int $paymentId,
        int $page = 1,
        int $itemsPerPage = 30,
    ): string {
        $response = $this->httpClient->request(
            'GET',
            sprintf(
                'orders/%s/payments/%d/methods',
                urlencode($tokenValue),
                $paymentId,
            ),
            ['query' => [
                'page' => $page,
                'itemsPerPage' => $itemsPerPage,
            ]],
        );

        return $response->getContent();
    }
}
