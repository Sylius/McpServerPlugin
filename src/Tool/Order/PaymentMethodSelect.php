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
    name: 'select_payment_method',
    description: 'select_payment_method(tokenValue, paymentId, paymentMethodCode) → raw JSON object of the order after choosing a payment method (checkoutState becomes “payment_selected”). Monetary amounts remain integers in minor units (÷100).',
)]
final readonly class PaymentMethodSelect
{
    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    /**
     * @param string $tokenValue Cart token; @param int $paymentId Payment ID; @param string $paymentMethodCode Code from list_payment_methods(), e.g. "bank_transfer"
     */
    public function __invoke(
        string $tokenValue,
        int $paymentId,
        string $paymentMethodCode,
    ): string {
        $response = $this->httpClient->request(
            'PATCH',
            sprintf('orders/%s/payments/%d', urlencode($tokenValue), $paymentId),
            [
                'json' => ['paymentMethod' => $paymentMethodCode],
            ],
        );

        return $response->getContent();
    }
}
