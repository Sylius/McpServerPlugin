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
use Symfony\Component\HttpFoundation\RequestStack;

#[McpTool(
    name: 'complete_checkout',
    description: 'complete_checkout(tokenValue) → JSON with checkout_url. Returns a URL the customer must open in their browser to review and pay for the order. The cart is already filled with address, shipping and payment — the customer just clicks the final "Pay" or "Place order" button.',
)]
final readonly class Complete
{
    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    /**
     * @param string $tokenValue Cart/order token from create_order / fetch_order.
     */
    public function __invoke(string $tokenValue): string
    {
        $baseUrl = $this->requestStack->getCurrentRequest()?->getSchemeAndHttpHost() ?? '';
        $checkoutUrl = $baseUrl . '/checkout/resume/' . urlencode($tokenValue);

        return (string) json_encode([
            'checkout_url' => $checkoutUrl,
            'message' => 'Share this URL with the customer to complete the purchase in their browser: ' . $checkoutUrl,
        ]);
    }
}
